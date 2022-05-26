<!DOCTYPE html>
       <html lang="en-US">
       <head>
           <meta charset="UTF-8">
           <meta name="viewport" content="width=device-width, initial-scale=1.0" />
           <style>
             body{
               font-family: roboto;
             }
           *{
             margin: 0;
             padding: 0;
           }
           .main{
               display: flex;
               flex-direction: column;
               padding: 2.2rem 2.2rem 1.2rem;
               align-items: center;
               text-align: center;
               background-image: url(http://ems.local/img/id-bg.svg);
               background-size: 100% 46%;
               background-repeat: no-repeat;
               position: relative;
               width: 300px;
           }
           .main .logo{
             max-width: 234px;
           }
           .main .image{
             display: flex;
             /* height: 116px;
             width: 116px;
             */
             height: 133px;
           width: 133px;
             margin: 1.7rem 0 1.8rem;
             border-radius: 100%;
             position: relative;
           }
           .main .image img{
             height: 100%;
             width: 100%;
           }
           h3{
             font-size: 22px;
           }
           .main .department{
             display: flex;
             justify-content: space-between;
             margin: 0.6rem 0;
             border: 1px solid #44318D;
             padding: 0.2rem 0.9rem;
             border-radius: 5px;
           }
           .main .department h4{
             font-weight: 400;
             font-size: 14px;
             margin-right: 0.2rem;
           }
           .main .department p{
             font-size: 14px;
           }
           .main h4{
             font-size: 13px;
           }
           .main .joining{
             margin: .5rem 0;
           }
           .main .barcode{
             display: flex;
             height: 36px;
             width: 142px;
           }
           .main .barcode img{
             height: 100%;
             width: 100%;
           }
           .main .address{
             position: relative;
             padding: 1.5rem 1.5rem 0;
             font-size: 13px;
             margin-top: 1.6rem;
             border-top: 1px solid #44318d;
           }
           </style>
       </head>
       <body>
         <div class="main">
             <img src="http://ems.local/idcard/tka-logo.svg" alt="tka-logo" class="logo">
             <span class="image">
               <img src="http://ems.local/idcard/employee.png" alt="employee">
             </span>
             <h3>Dheeraj Arora</h3>
             <div class="department">
               <h4>Department - </h4>
               <p>Sales Support</p>
             </div>
             <h4>Employee ID : <span>123</span></h4>
             <h4 class="joining">Joining Date: <span>21-Apr-2021</span></h4>
             <span class="barcode">
               <img src="http://ems.local/idcard/barcode.png" alt="barcode">
             </span>
             <p class="address">
             <span></span>
             Property of The Knowledge Academy <br> Tel: 8847038034</p>
         </div>
       </body>
       </html>
       