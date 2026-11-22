<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Booking Confirmed | Blaze Rental</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body { 
        background:#f7f9fc; 
        min-height:100vh; 
        display:flex; 
        align-items:center; 
        justify-content:center; 
        margin: 0; 
        font-family: Arial, sans-serif;
    }
    
    .card { 
        width: 560px; 
        max-width:94%; 
        border:0; 
        border-radius:14px; 
        box-shadow:0 10px 28px rgba(0,0,0,.08); 
        background: white; 
    }
    
    .ok { 
        width:52px; 
        height:52px; 
        border-radius:50%; 
        background:#eaf6ec; 
        color:#1e7e34; 
        display:flex; 
        align-items:center; 
        justify-content:center; 
        font-size:26px; 
    }
    
    .kv{ font-weight:600; color:#333; }
    .vv{ color:#0a58ca; }
  </style>
</head>
<body>

  <div class="card p-4 p-md-5">
    <div class="d-flex align-items-center gap-3 mb-3">
      <div class="ok">✓</div>
      <div>
        <h4 class="m-0">Booking Confirmed</h4>
        <small class="text-success">Your booking request has been submitted successfully.</small>
      </div>
    </div>

    <div class="row g-2 mb-4">
      <div class="col-sm-6"><span class="kv">Name:</span> <span id="b_name" class="vv">-</span></div>
      <div class="col-sm-6"><span class="kv">Vehicle:</span> <span id="b_model" class="vv">-</span></div>
      <div class="col-sm-6"><span class="kv">Pickup:</span> <span id="b_pickup" class="vv">-</span></div>
      <div class="col-sm-6"><span class="kv">Drop-off:</span> <span id="b_drop" class="vv">-</span></div>
      <div class="col-sm-6"><span class="kv">Total Hours:</span> <span id="hours" class="vv">-</span></div>
      <div class="col-sm-6"><span class="kv">Total:</span> <span id="b_total" class="vv">₹0</span></div>
    </div>

    <div class="d-flex gap-2">
        <a href="mybookings.php" class="btn btn-dark w-50">View My Bookings</a>
        <a href="index.php" class="btn btn-outline-secondary w-50">Go to Home</a>
    </div>
  </div>

  <script>
    // URL Parsing JS
    const p = new URLSearchParams(location.search);
    const set=(id,k)=>{const e=document.getElementById(id);const v=p.get(k);e.textContent=v&&v!=='null'?v:'-';};
    set('b_name','name'); set('b_model','model'); set('b_pickup','pu'); set('b_drop','do'); set('hours','hours');
    
    const t = p.get('total');
    document.getElementById('b_total').textContent = t ? '₹' + Number(t).toLocaleString('en-IN') : '₹0';

    function toAMPM(str) {
      if (!str) return "-";
      // Ensure cross-browser date parsing by replacing dashes if necessary
      const d = new Date(str.replace(/-/g, "/"));
      if (isNaN(d)) return str; // fallback if invalid date
      let h = d.getHours(), m = d.getMinutes();
      let ampm = h >= 12 ? 'PM':'AM';
      h = h % 12 || 12;
      let mm = m < 10 ? '0'+m : m;
      return d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+h+':'+mm+' '+ampm;
    }
    
    document.getElementById('b_pickup').textContent = toAMPM(p.get('pu'));
    document.getElementById('b_drop').textContent   = toAMPM(p.get('do'));
  </script>
</body>
</html>