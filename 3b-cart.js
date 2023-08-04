var cart = {
  // (A) HELPER - AJAX FETCH
  ajax : (data, after) => {
    // (A1) FORM DATA
    let form = new FormData();
    for (let [k, v] of Object.entries(data)) { form.append(k, v); }

    // (A2) FETCH
    fetch("2-ajax-cart.php", { method:"POST", body:form })
    .then(res => res.json())
    .then(res => {
      if (res.status==1) { after(res); }
      else { alert(res.msg); }
    })
    .catch(err => console.error(err));
  },

  // (B) HELPER - TOGGLE HTML SECTIONS
  toggle : (target, show) => {
    if (show) { target.className = "show"; }
    else { target.className = ""; }
  },

  // (C) INITIALIZE
  hCart : null, // html cart
  hWCart : null, // html cart wrapper
  hCCart : null, // html cart count
  hCO : null, // html checkout wrapper
  init : () => {
    // (C1) GET HTML ELEMENTS
    cart.hCart = document.getElementById("cart");
    cart.hWCart = document.getElementById("wCart");
    cart.hCCart = document.getElementById("cCart");
    cart.hCO = document.getElementById("checkout");

    // (C2) UPDATE CART COUNT
    cart.ajax({ req : "count" }, res => cart.count(res.count));
  },

  // (D) UPDATE CART COUNT
  count : qty => {
    cart.hCCart.innerHTML = qty;
    if (cart.hWCart.classList.contains("show")) { cart.show(); }
  },

  // (E) SHOW CART ITEMS
  show : () => {
    // (E1) RESET
    cart.toggle(cart.hWCart, true);
    cart.hCart.innerHTML = "";

    // (E2) LOAD & GENERATE HTML
    cart.ajax({ req : "get" }, items => {
      // (E2-1) ITEMS IN CART
      items = items.msg;

      // (E2-2) CART IS EMPTY
      if (items==null) {
        cart.hCart.innerHTML = `<div class="cCell empty">Cart is empty</div>`;
      }

      // (E2-3) DRAW CART ITEMS
      else {
        let row, subtotal, total = 0;
        for (let [id, item] of Object.entries(items)) {
          // CALCULATE SUBTOTAL
          subtotal = item["price"] * item["qty"];
          total += subtotal;

          // ITEM ROW
          row = document.createElement("div");
          row.className = "cCell";
          row.innerHTML = `<input class="cQty" type="number" value="${item["qty"]}" 
          min="0" max="99" onchange="cart.change(${id}, this.value)">
          <div class="cInfo">
            <div class="cName">${item["name"]}</div>
            <div class="cPrice">$${subtotal.toFixed(2)}</div>
          </div>
          <input class="cDel button" type="button" value="X" onclick="cart.remove(${id})">`;
          cart.hCart.appendChild(row);
        }

        // CART TOTALS
        row = document.createElement("div");
        row.className = "cCell";
        row.innerHTML = `<input class="cDel button" type="button" value="X" onclick="cart.empty()">
        <div class="cInfo">
          <div class="cName">Total</div>
          <div class="cPrice">$${total.toFixed(2)}</div>
        </div>
        <input class="cDel button" type="button" value="&gt;" onclick="cart.toggle(cart.hCO, true)">`;
        cart.hCart.appendChild(row);
      }
    });
  },

  // (F) ADD ITEM TO CART
  add : id => cart.ajax(
    { req : "set", id : id },
    res => cart.count(res.count)
  ),

  // (G) CHANGE ITEM QTY
  change : (id, qty) => cart.ajax(
    { req : "set", id : id, qty : qty },
    res => cart.count(res.count)
  ),

  // (H) REMOVE ITEM
  remove : id => cart.ajax(
    { req : "set", id : id, qty : 0 },
    res => cart.count(res.count)
  ),

  // (I) NUKE
  empty : () => { if (confirm("Reset cart?")) {
    cart.ajax(
      { req : "nuke" },
      res => cart.count(res.count)
    ); 
  }},

  // (J) CHECKOUT
  checkout : () => {
    cart.ajax({
      req : "checkout",
      name : document.getElementById("coName").value,
      email : document.getElementById("coEmail").value
    }, res => location.href = "3c-thank-you.html"); 
    return false;
  }
};
window.onload = cart.init;