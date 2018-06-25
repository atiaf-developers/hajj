<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\Resturant;
use Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use DB;
use App\Models\Rate;

class OrdersController extends FrontController {

    public function __construct() {
        parent::__construct();
        $this->middleware('auth');
    }

    public function index(Request $request) {
        try {
            if (!in_array($request->type, ['current', 'completed'])) {
                return $this->err404();
            }
            $user = $this->User;
            $orders = Order::with('order_meals');
            $orders->where('orders.user_id', $user->id);
            if ($request->type == 'current') {
                $orders->whereIn('status', [0, 1, 2, 4]);
            } else {
                $orders->where('status', 3);
            }
            $orders->join('resturantes', 'orders.resturant_id', '=', 'resturantes.id');
            $orders->join('resturant_branches', 'resturant_branches.id', '=', 'orders.resturant_branch_id');
            $orders->join('cities', 'resturant_branches.region_id', '=', 'cities.id');
            $orders->select('orders.*', 'resturantes.title_' . $this->lang_code . ' as resturant', 'resturant_branches.slug as resturant_slug', 'resturantes.image', 'cities.title_' . $this->lang_code . ' as region');


            $orders = $orders->paginate($this->limit);

            $orders->getCollection()->transform(function($order) {
                return Order::transformForPagination($order);
            });

            //$orders = Order::transformCollection($orders,'ForPagination');
            $this->data['orders'] = $orders;
            if ($request->type == 'current') {
                return $this->_view('orders.current');
            } else {
                return $this->_view('orders.completed');
            }
        } catch (\Exception $e) {
            //dd($e->getMessage());
            session()->flash('msg', _lang('app.error_is_occured'));
            //return redirect()->route('user_orders');
        }
    }

    public function edit($id) {
        try {
            $order_id = decrypt($id);
            $order_id = 53;

            $order = Order::join('addresses', 'orders.user_address_id', '=', 'addresses.id')
                    ->select('orders.id as order_id', 'orders.created_at as order_created_at', 'orders.vat', 'orders.primary_price', 'orders.total_cost', 'orders.toppings_price', 'orders.coupon', 'orders.service_charge', 'orders.delivery_cost', 'orders.status', 'orders.is_rated', 'addresses.*')
                    ->where('orders.id', $order_id)
                    ->where('orders.user_id', $this->User->id)
                    ->first();
            if (!$order) {
                return $this->err404();
            }
        } catch (DecryptException $ex) {
            return $this->err404();
        }
        $order_meals = OrderMeal::where('order_id', $order_id)
                ->join('meals', 'order_meals.meal_id', '=', 'meals.id')
                ->leftJoin('meal_sizes', 'order_meals.meal_size_id', '=', 'meal_sizes.id')
                ->leftJoin('sizes', 'meal_sizes.size_id', '=', 'sizes.id')
                ->select('order_meals.*', 'meals.title_' . $this->lang_code . ' as meal_title', 'sizes.title_' . $this->lang_code . ' as size_title')
                ->get();
        $order_meals = OrderMeal::transformCollection($order_meals, 'EditOrder');

        $this->data['order'] = $order;
        $this->data['order_meals'] = $order_meals;
        return $this->_view('orders.edit');
    }

    public function updateOrderMealQuantity(Request $request) {
        //dd($request->all());
        $id = $request->input('id');
        $quantity = $request->input('qty');
        $OrderMeal = OrderMeal::find($id);
        if (!$OrderMeal) {
            
        }

        DB::beginTransaction();
        try {
            $Order = Order::find($OrderMeal->order_id);
            if (!$this->canEditOrder($Order)) {
                return _json('error', _lang('app.time_out_for_editing_order'));
            }
            $NewOrderMealCost = ($OrderMeal->cost_of_meal + $OrderMeal->toppings_price) * $quantity;
            $primary_price = ($Order->primary_price - $OrderMeal->cost_of_quantity) + $NewOrderMealCost;
            //dd($NewOrderMealCost);
            $PriceList['primary_price'] = $primary_price;
            $PriceList['vat_cost'] = (($PriceList['primary_price'] * $Order->vat) / 100);
            $PriceList['service_charge'] = (($PriceList['primary_price'] * $Order->service_charge) / 100);
            $PriceList['total_price'] = $PriceList['primary_price'] + $PriceList['vat_cost'] + $PriceList['service_charge'] + $Order->delivery_cost;

            $Order->primary_price = $PriceList['primary_price'];
            $Order->total_cost = $PriceList['total_price'];
            $Order->save();
            $OrderMeal->cost_of_quantity = $NewOrderMealCost;
            $OrderMeal->quantity = $quantity;
            $OrderMeal->save();
            DB::commit();
            return _json('success', $PriceList);
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', $ex->getMessage());
        }
    }

    public function removeOrderMeal(Request $request) {
        //dd($request->all());
        $id = $request->input('id');
        $OrderMeal = OrderMeal::find($id);
        if (!$OrderMeal) {
            return _json('error', _lang('app.order_meal_is_not_found'));
        }

        DB::beginTransaction();
        try {
            $Order = Order::find($OrderMeal->order_id);
            if (!$this->canEditOrder($Order)) {
                return _json('error', _lang('app.time_out_for_editing_order'));
            }
            $OrderMealsCount = OrderMeal::where('order_id', $OrderMeal->order_id)->count();
            //dd($OrderMealsCount);
            if ($OrderMealsCount == 1) {
                $Order->delete();
                $response = _url('user-orders?type=current');
            } else {
                $primary_price = ($Order->primary_price - $OrderMeal->cost_of_quantity);
                //dd($NewOrderMealCost);
                $PriceList['primary_price'] = $primary_price;
                $PriceList['vat_cost'] = (($PriceList['primary_price'] * $Order->vat) / 100);
                $PriceList['service_charge'] = (($PriceList['primary_price'] * $Order->service_charge) / 100);
                $PriceList['total_price'] = $PriceList['primary_price'] + $PriceList['vat_cost'] + $PriceList['service_charge'] + $Order->delivery_cost;

                $Order->primary_price = $PriceList['primary_price'];
                $Order->total_cost = $PriceList['total_price'];
                $Order->save();
                $OrderMeal->delete();
                $response = $PriceList;
            }
            DB::commit();
            return _json('success', $response);
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', $ex->getMessage());
        }
    }

    public function show($id) {
        try {
            $order_id = decrypt($id);
            // dd($order_id);
            $user = $this->User;

            $order = Order::join('addresses', 'orders.user_address_id', '=', 'addresses.id')
                    ->select('orders.id as order_id', 'orders.acceptance_date as acceptance_date', 'orders.vat', 'orders.primary_price', 'orders.total_cost', 'orders.toppings_price', 'orders.coupon', 'orders.service_charge', 'orders.delivery_cost', 'orders.status', 'orders.is_rated', 'addresses.*')
                    ->where('orders.id', $order_id)
                    ->where('orders.user_id', $user->id)
                    ->first();
            //dd($order);

            $order_meals = OrderMeal::where('order_id', $order_id)
                    ->join('meals', 'order_meals.meal_id', '=', 'meals.id')
                    ->leftJoin('meal_sizes', 'order_meals.meal_size_id', '=', 'meal_sizes.id')
                    ->leftJoin('sizes', 'meal_sizes.size_id', '=', 'sizes.id')
                    ->select('order_meals.*', 'meals.title_' . $this->lang_code . ' as meal_title', 'sizes.title_' . $this->lang_code . ' as size_title')
                    ->get();

            if (!$order) {
                return $this->err404();
            }
            $order_time = strtotime($order->acceptance_date);
            $now_time = strtotime(date('Y-m-d H:i:s'));
            $minutes = ($now_time - $order_time) / 60;
            $this->data['order'] = $order;
            $this->data['order_meals'] = $order_meals;
            $this->data['minutes'] = $minutes;


            return $this->_view('orders.show');
        } catch (\Exception $e) {

            session()->flash('msg', _lang('app.error_is_occured'));
            return redirect()->route('user_orders');
        }
    }

    public function rate(Request $request) {
        DB::beginTransaction();
        try {
            $order_id = decrypt($request->order_id);
            $order = Order::find($order_id);

            if (!$order) {

                return $this->err404();
            }
            if (!$request->rate) {
                return redirect()->back();
            }
            $user = $this->User;
            $rate = new Rate;
            $rate->user_id = $user->id;
            $rate->resturant_id = $order->resturant_id;
            $rate->order_id = $order->id;
            $rate->rate = $request->rate;
            if ($request->opinion) {
                $rate->opinion = $request->opinion;
            }
            $rate->save();
            $order->is_rated = true;
            $order->save();

            $resturant_rate = Rate::where('resturant_id', $order->resturant_id)
                    ->select(DB::raw(' SUM(rate)/COUNT(*) as rate'))
                    ->first();


            $resturant = Resturant::find($order->resturant_id);
            $resturant->rate = $resturant_rate->rate;
            $resturant->save();


            DB::commit();

            session()->flash('msg', _lang('app.rated_successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('msg', _lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

    public function destroy($id) {
    
        try {
            $order_id = decrypt($id);
            $Order = Order::find($order_id);
           
            if (!$Order) {
                return _json('error', _lang('app.order_is_not_found'));
            }
            if(!$this->canEditOrder($Order)){
                return _json('error', _lang('app.time_out_for_cancelling_order'));
            }
            $Order->delete();
            return _json('success', _url('user-orders?type=current'));
        } catch (DecryptException $ex) {
            return _json('error',_lang('app.error_is_occured'));
        } catch (\Exception $ex) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    private function canEditOrder($order) {
        $order_time = strtotime($order->acceptance_date);
        $now_time = strtotime(date('Y-m-d H:i:s'));
        $minutes = ($now_time - $order_time) / 60;

        if ($minutes < $this->order_minutes_limit) {
            return true;
        }
        return false;
    }

}
