


<div style="text-align: right; margin: 0; padding: 0; border: 0; text-decoration: none; list-style: none; direction: rtl; font: 14px/28px Tahoma, Geneva, sans-serif; color: #232323; padding: 0px 15px;">
    <header class="header" style="float: right; width: 100%; padding-bottom: 15px; border-bottom: 2px solid #eaeaea;">
        <div class="container-fluid" style="float: right; width: 100%;">
            <div class="logo" style="float: right; margin-top: 15px;">
                <img style="width: 190px;" src="{{ url('public/backend/images/logo.png')  }}" title="موكا بوك">
            </div>
            <div class="leftexttop" style="float: left; margin-top: 26px;">

                <p> رقم الطلب<span> {{  $Order->id  }}</span></p>
                <p> رقم الفاتورة<span> {{  $Order->invoice->id  }}</span></p>
                <p> وفت انشاء الطلب: <span> {{  $Order->created_at  }}</span></p>
            </div>
        </div>
        <!--/.container-fluid--> 

    </header>
    <!--//.header-->

    <div class="container-fluid" style="float: right; width: 100%;">
        <div class="ribox" style="float: right; width: 50%; margin-top: 30px;">
            <h2 style="margin-bottom: 10px;">تفاصيل الطلب</h2>
            <p>الخدمة :<span> {{  $Order->category->title_ar  }}</span></p>
            <p>اجمالى السعر:<span> {{  $Order->invoice->total_price  }}</span></p>
            <p>طريقة الدفع :<span> {{ $payment_method[$Order->invoice->payment_method]  }}</span></p>
            <p>تاريخ الضمان :<span> {{ $Order->invoice->expire_gaurantee_date  }}</span></p>
        </div>
        <div class="ribox" style="float: right; width: 50%; margin-top: 30px;">
            <h2 style="margin-bottom: 10px;">بيانات العميل</h2>
            <p>الاسم:<span> {{$Order->client->client->name}}</span></p>
            <p>البريد الإلكترونى:<span> {{$Order->client->email}}</span></p>
            <p>الموبايل:<span> {{$Order->client->mobile}}</span></p>
           
        </div>
        <!--ribox-->

        <!--ribox-->


        
        
        <table width="100%" border="0" align="center" class="table table-striped"   style=" float:right;margin-top:30px;">
            <thead>
                <tr>
                    <th style="padding: 8px; line-height: 1.42857143; vertical-align: top; border: 1px solid #ddd;">وصف</th>
                    <th style="padding: 8px; line-height: 1.42857143; vertical-align: top; border: 1px solid #ddd;">سعر</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   
                    <td style="padding: 8px; line-height: 1.42857143; vertical-align: top; border: 1px solid #ddd;">
                        {{ $Order->invoice->price_details->fix->text}}
                    </td>
                    <td style="padding: 8px; line-height: 1.42857143; vertical-align: top; border: 1px solid #ddd;">
                        {{ $Order->invoice->price_details->fix->price}}
                    </td>
                </tr>
                <tr>
                    
                    <td colspan="3" style="padding: 8px; line-height: 1.42857143; vertical-align: top; border: 1px solid #ddd;">
                        المواد
                    </td>
                </tr>
     
                @if(isset($Order->invoice->price_details->material))
                @foreach($Order->invoice->price_details->material as $one)
                <tr>
                   
                    <td style="padding: 8px; line-height: 1.42857143; vertical-align: top; border: 1px solid #ddd;">
                        {{  $one->text  }}
                    </td>
                    <td style="padding: 8px; line-height: 1.42857143; vertical-align: top; border: 1px solid #ddd;">
                        {{  $one->price  }}
                    </td>
                </tr>
                @endforeach
                @endif


            </tbody>
        </table>
        <div class="ribox innerbac" style="float: left; width: 33.333%; margin-top: 30px;background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-bottom: 30px;">
     
            <p> السعر<span> {{$Order->invoice->fix_price+$Order->invoice->material_price}}</span></p>
            <p>الضريبة {{$Order->invoice->vat.' %'}} <span> {{$Order->invoice->vat_price}}</span></p>
            <p>اجمالى السعر<span> {{$Order->invoice->total_price}}</span></p>

        </div>

    </div>
    <!--/.container-fluid-->

</div>

