<!DOCTYPE html>
<html lang="en">

<head>
   <?php include("includes/compatibility.php"); ?>
   <meta name="description" content="">
   <title>Home</title>
   <?php include("includes/style.php"); ?>
</head>

<body>
   <div class="dashboard">
      <?php include("includes/sidebar.php"); ?>

      <main class="main-wrapper">
         <?php include("includes/top-header.php"); ?>

         <!-- 
            <section class="dashboard-page-main-wrapper d-flex">
            <div class="total-number-section-wrapper">

               <div class="d-flex align-items-streach">
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/sales.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>5000</span>
                        <span>Total Sales</span>
                     </div>
                  </div>
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/profit.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>10,000</span>
                        <span>Total Profit</span>
                     </div>
                  </div>
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/Revenue.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>2000</span>
                        <span>Total Revenue</span>
                     </div>
                  </div>
               </div>

               <div class="d-flex align-items-streach">
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/orders.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>50,000</span>
                        <span>Total Orders</span>
                     </div>
                  </div>
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/products.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>100,000</span>
                        <span>Total Products</span>
                     </div>
                  </div>
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/Location-bg.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>100</span>
                        <span>Total Location</span>
                     </div>
                  </div>
               </div>

               <div class="d-flex align-items-streach">
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/employees.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>600</span>
                        <span>Total Employees</span>
                     </div>
                  </div>
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/categories.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>500</span>
                        <span>Total Categories</span>
                     </div>
                  </div>
                  <div class="total-number-wrapper d-flex align-items-center justify-content-center">
                     <div class="total-number-image">
                        <img src="./assets/images/icons/Amount.png" alt="">
                     </div>
                     <div class="total-number-counting">
                        <span>50000</span>
                        <span>Total Amount</span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="dashboard-notification-wrapper d-flex flex-column justify-content-between">
               <div class="dashboard-notification-header d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                     <span><img src="./assets/images/icons/Notification.png" alt=""></span>
                     <span>Notification</span>
                  </div>
                  <div><a href="#">See All</a></div>
               </div>
               <div class="dashboard-notification-list">
                  <ul class="m-0 p-0">
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="notification-badge d-flex align-items-center justify-content-center">
                                 A
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="notification-small-heading">Recently Payment</span>
                                 <span class="notification-description">$255 payment Recieved</span>
                              </span>
                           </div>
                           <div>
                              <span class="notification-date">31 oct 2023</span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="notification-badge d-flex align-items-center justify-content-center">
                                 A
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="notification-small-heading">1 message</span>
                                 <span class="notification-description">From Ryan</span>
                              </span>
                           </div>
                           <div>
                              <span class="notification-date">31 oct 2023</span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="notification-badge d-flex align-items-center justify-content-center">
                                 A
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="notification-small-heading">Recently Payment</span>
                                 <span class="notification-description">$255 payment Recieved</span>
                              </span>
                           </div>
                           <div>
                              <span class="notification-date">31 oct 2023</span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="notification-badge d-flex align-items-center justify-content-center">
                                 A
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="notification-small-heading">Recently Payment</span>
                                 <span class="notification-description">$255 payment Recieved</span>
                              </span>
                           </div>
                           <div>
                              <span class="notification-date">31 oct 2023</span>
                           </div>
                        </a>
                     </li>
                  </ul>
               </div>
            </div>
         </section>

         <section class="dashboard-page-main-wrapper d-flex">
            <div class="dashboard-store-wrapper">
               <div class="dashboard-store-header d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                     <span><img src="./assets/images/icons/Notification.png" alt=""></span>
                     <span>Stores</span>
                  </div>
                  <div><a href="#">See All</a></div>
               </div>
               <div class="dashboard-store-table-wrapper">
                  <table>
                     <thead>
                        <tr>
                           <th>Stores</th>
                           <th>Location</th>
                           <th>Address</th>
                           <th>Company</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td>Warehouse</td>
                           <td>WH</td>
                           <td>123 some road, Maitland</td>
                           <td>Korea Market</td>
                        </tr>
                        <tr>
                           <td>Shop</td>
                           <td>SH</td>
                           <td>123 some road, Lululand</td>
                           <td>Korea Market</td>
                        </tr>
                        <tr>
                           <td>Gogi Observatory</td>
                           <td>GoOb</td>
                           <td>123 some road, Lululand</td>
                           <td>Gogi</td>
                        </tr>
                        <tr>
                           <td>Shop</td>
                           <td>SH</td>
                           <td>123 some road, Lululand</td>
                           <td>Korea Market</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="dashboard-location-wrapper">
               <div class="dashboard-location-header d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                     <span><img src="./assets/images/icons//location.png" alt=""></span>
                     <span>Locations</span>
                  </div>
                  <div><a href="#">See All</a></div>
               </div>
               <div class="dashboard-location-list">
                  <ul class="m-0 p-0">
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="location-badge d-flex align-items-center justify-content-center">
                                 <img src="./assets/images/icons/location-purple.png" alt="">
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="location-small-heading">Locations</span>
                                 <span class="location-description">WH, Korea Market</span>
                              </span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="location-badge d-flex align-items-center justify-content-center">
                                 <img src="./assets/images/icons/location-purple.png" alt="">
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="location-small-heading">Shop</span>
                                 <span class="location-description">WH, Korea Market</span>
                              </span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="location-badge d-flex align-items-center justify-content-center">
                                 <img src="./assets/images/icons/location-purple.png" alt="">
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="location-small-heading">Locations</span>
                                 <span class="location-description">WH, Korea Market</span>
                              </span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="location-badge d-flex align-items-center justify-content-center">
                                 <img src="./assets/images/icons/location-purple.png" alt="">
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="location-small-heading">Shop</span>
                                 <span class="location-description">WH, Korea Market</span>
                              </span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="location-badge d-flex align-items-center justify-content-center">
                                 <img src="./assets/images/icons/location-purple.png" alt="">
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="location-small-heading">Locations</span>
                                 <span class="location-description">WH, Korea Market</span>
                              </span>
                           </div>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="d-flex align-items-top justify-content-between">
                           <div class="d-flex">
                              <span class="location-badge d-flex align-items-center justify-content-center">
                                 <img src="./assets/images/icons/location-purple.png" alt="">
                              </span>
                              <span class="d-flex flex-column">
                                 <span class="location-small-heading">Shop</span>
                                 <span class="location-description">WH, Korea Market</span>
                              </span>
                           </div>
                        </a>
                     </li>
                  </ul>
               </div>
            </div>
         </section>
         -->


         <section class="dashboard-page-main-wrapper">
            <div class="text-center" style="    background-color: #fff;padding: 25px;border-radius: 12px;width:100%">
               <h2>Welcome Rajesh</h2>
            </div>
         </section>
      </main>
   </div>

   <?php include("includes/scripts.php"); ?>
</body>

</html>