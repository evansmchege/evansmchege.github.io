<?php
if (isset($_POST["req"])) {
  // (A) INIT SHOPPING CART
  session_start();
  if (!isset($_SESSION["cartI"])) {
    $_SESSION["cartI"] = []; // cart items
    $_SESSION["cartC"] = 0;  // total quantity
  }

  // (B) UPDATE CART COUNT
  function ccount () {
    $_SESSION["cartC"] = 0;
    if (count($_SESSION["cartI"])!=0) {
      foreach ($_SESSION["cartI"] as $id=>$qty) { $_SESSION["cartC"] += $qty; }
    }
  }

  // (C) STANDARD SYSTEM RESPONSE
  function respond ($status=1, $msg="OK") {
    echo json_encode(["status"=>$status, "msg"=>$msg, "count" => $_SESSION["cartC"]]);
  }

  // (D) CART ACTIONS
  switch ($_POST["req"]) {
    // (D1) GET COUNT
    case "count": respond(); break;

    // (D2) ADD / CHANGE QUANTITY / REMOVE
    // send id only to add item
    // send id and qty to set quantity
    // send id and 0 qty to remove item
    case "set":
      $max = 99; // max allowed quantity per item
      $item = &$_SESSION["cartI"][$_POST["id"]];
      if (isset($_POST["qty"])) { $item = $_POST["qty"]; }
      else { if (isset($item)) { $item++; } else { $item = 1; } }
      if ($item<=0) { unset($_SESSION["cartI"][$_POST["id"]]); }
      if ($item > $max) { $item = $max; }
      ccount(); respond(); break;

    // (D3) NUKE
    case "nuke":
      $_SESSION["cartI"] = [];
      $_SESSION["cartC"] = 0;
      respond(); break;

    // (D4) GET ALL ITEMS IN CART
    case "get":
      // (D4-1) CART IS EMPTY
      if ($_SESSION["cartC"]==0) { respond(1, null); break; }

      // (D4-2) GET PRODUCTS + FILTER ILLEGAL
      require "1-products.php";
      $items = [];
      foreach ($_SESSION["cartI"] as $id=>$qty) {
        if (isset($products[$id])) {
          $items[$id] = $products[$id];
          $items[$id]["qty"] = $qty;
        } else {
          $_SESSION["cartC"] -= $_SESSION["cartI"][$id];
          unset($_SESSION["cartI"][$id]);
        }
      }
      if ($_SESSION["cartC"]==0) { respond(1, null); break; }
      respond(1, $items); break;

    // (D5) CHECKOUT
    case "checkout":
      // (D5-1) CART IS EMPTY
      if ($_SESSION["cartC"]==0) { respond(0, "Cart Empty"); break; }

      // (D5-2) EMAIL TO ADMIN
      require "1-products.php";
      $to = "admin@site.com";
      $subject = "Order Received";
      $body = "Name: " . $_POST["name"] . "\r\n";
      $body .= "Email: " . $_POST["email"] . "\r\n";
      foreach ($_SESSION["cartI"] as $id=>$qty) {
        $body .= sprintf("%s X %s\r\n", $qty, $products[$id]["name"]);
      }
      if (mail($to, $subject, $body)) {
        $_SESSION["cartI"] = [];
        $_SESSION["cartC"] = 0;
        respond();
      } else { respond(0, "ERROR SENDING MAIL"); }
      break;
  }
}