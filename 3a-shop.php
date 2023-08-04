<!DOCTYPE html>
<html>
  <head>
    <title>Shopping Page Demo</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="3a-shop.css">
    <script src="3b-cart.js"></script>
  </head>
  <body>

  <div>
      <h1 class="text-center">BEB'S PHOTOSHOP💛💛❤️‍🔥</h1>
   </div>

    <!-- (A) PRODUCTS + SHOPPING CART -->
    <div id="wrap">
      <!-- (A1) HEADER -->
      <div id="head">
        <div id="iCart" onclick="cart.show()">
          My Cart <span id="cCart">0</span>
        </div>
      </div>

      <!-- (A2) PRODUCTS -->
      <div id="products"><?php
require "1-products.php";
foreach ($products as $i => $p) {?>
        <div class="pCell">
          <img class="pImg" src="<?=$p["image"]?>">
          <div class="pName"><?=$p["name"]?></div>
          <div class="pprice">ksh <?=$p["price"]?></div>
          <input class="pAdd button" type="button" value="Add To Cart" onclick="cart.add(<?=$i?>)">
        </div>
        <?php }?>
      </div>

      <!-- (A3) CART ITEMS -->
      <div id="wCart">
        <span id="wCartClose" class="button" onclick="cart.toggle(cart.hWCart, false)">&#8678;</span>
        <h2>SHOPPING CART</h2>
        <div id="cart"></div>
      </div>
    </div>

    <!-- (B) CHECKOUT FORM -->
    <div id="checkout"><form onsubmit="return cart.checkout()">
      <div id="coClose" class="button" onclick="cart.toggle(cart.hCO, false)">X</div>
      <label>Name</label>
      <input type="text" id="coName" required value="Jon Doe">
      <label>Email</label>
      <input type="email" id="coEmail" required value="jon@doe.com">
      <input class="button" type="submit" value="Checkout">
    </form></div>
  </body>
</html>