<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>الهوية</title>
  <style type="text/css">
</style>
</head>
<body style="position: relative;width: 650px; margin: 0 auto; color: #555555; background: #FFFFFF; font-family: sans-serif;font-size: 14px;">


  <div>
    <div class="top-header" style="background:#86cfc2; padding: 15px 0;">
    </div>
    <div class="content" style="background-image: linear-gradient( #6a7ca0, #a0c2de ); width:100%; padding:10px 0;" >
      <header class="clearfix" style="padding: 10px 0;margin-bottom: 10px; padding: 0 5px;">
        <div class="bottom-header" style="display: block; padding:0 5px">
          <h2 style="color:#fff; font-size: 36px; text-align: right;font-family: sans-serif;margin:0; font-weight: bold;padding-right:30px;">شـركــــة</h2>
          <h3 style="margin:0; color:red; font-size: 28px; text-shadow:1px 1px #fff; text-align: left; direction: rtl;">مخيم رقم ( 716/5 )</h3>
        </div>
        <div id="logo" style="width:70%;margin: 0 auto;display: block;">
          <img src= "{{ url('public/backend/images') }}/logo-card-header.png" style="">
        </div>
      </header>
      <main style="width: 100%;display: block; float: left;height: 670px;margin-top: 30px;">

       <div style="margin:10px 0;">
        <div id="details" style="margin: 0;width:100%; display: block;height: 270px;">
          <div id="invoice" style="float: left;width:100%;padding:0;display:block;">

            <div style="width:33%;float: right; margin: 0 auto;">
              <img src="{{$pilgrim->image}}" alt="" style="width:100%;height: 310px; display: block; ">
            </div>

          </div>
        </div>  
      </div>

      
      <div id="text" style="width:100%; display:block; float: left;margin:50px 0 10px;padding-right:30px;">
        <div class="name" style="width:100%; display:block; direction:rtl;text-align:right; float:left; margin:10px 0; line-height:30px;">
          <p style=" font-size:40px;font-weight:bold; color:#000;text-shadow: 1px 1px #fff;width: 30%; float: right;padding: 5px 10px; margin: 0;">اسم الحاج :</p>
          <p style="padding:5px 10px; margin: 0;font-size: 40px;color: #000;"> {{$pilgrim->name}}</p>
        </div>
        <div class="name" style="width:100%; display:block; direction:rtl;text-align:right; float:left; margin:10px 0; line-height:30px;">
          <p style=" font-size:40px;font-weight:bold; color:#000;text-shadow: 1px 1px #fff;width: 30%; float: right;padding: 5px 10px; margin: 0;">رقم الهوية :</p>
          <p style="padding: 5px 10px; margin: 0;font-size: 40px;color: #000;"> {{$pilgrim->ssn}}</p>
        </div>
        <div class="name" style="width:100%; display:block; direction:rtl;text-align:right; float:left; margin:10px 0; line-height:30px;">
          <p style=" font-size:40px;font-weight:bold; color:#000;text-shadow: 1px 1px #fff;width: 30%; float: right;padding: 5px 10px; margin: 0;">رقم الحاج :</p>
          <p style="padding: 5px 10px; margin: 0;font-size: 40px;color: #000;"> {{$pilgrim->code}}</p>
        </div>
        <div class="name" style="width:100%; display:block; direction:rtl;text-align:right; float:left; margin:10px 0; line-height:30px;">
            @foreach($pilgrim->accommodation as $one)
             <p style=" font-size:35px;font-weight:bold; color:#000;text-shadow: 1px 1px #fff;width: 15%; float: right;padding: 5px 10px; margin: 0;">{{ _lang('app.'.$one['name'],'ar')}}:</p>
             <p style="padding: 5px 10px; margin: 0;font-size: 35px;color: #000; float: right; width:10%;"> {{ $one['value']}}</p>
         @endforeach
         
          
         
        </div>
      </div>

      
    </main>
    <div id="conatct" style="padding:10px 0;width:100%; display:block; float: left; background:#fff;margin: 5px 0; margin-top:20px; height:20px; ">

        <div style="width: 40%; float: left; margin-left:30px">
          <img src= "{{ url('public/backend/images') }}/smartphone.png" alt="" style="float:left; padding:0 5px; width:10%; height:40px;">
          <p style="float: left;color: #6c7fa3; font-size: 30px; padding:5px; margin: 0;font-weight: bold;padding-right: 10px;">0507655524</p>
        </div>

        <div style="width: 40%; float: right;">
          <img src= "{{ url('public/backend/images') }}/telephone.png" alt="" style="float:left; padding:5px 10px; width:4.5%; height:28px;">
          <p style="float: right;color: #6c7fa3; font-size: 30px; padding:5px; margin: 0;font-weight: bold; position: fixed; right: 0">0125301111</p>

        </div>
      </div>


  </div>



</div>

</body>
</html>