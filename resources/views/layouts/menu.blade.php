
<!doctype html>
<html lang="en">
  <head>
  	<title>Website menu 04</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="css/style.css">

	</head>
	<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/">Produccion por dia</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/production_period">Produccion por periodo</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/final_salary">Sueldo final</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Gestion
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/employee">Empleado</a></li>
            <li><a class="dropdown-item" href="/biweekly">Quincena</a></li>
            <li><a class="dropdown-item" href="/stages">Producto</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
	<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"935b6d10fc631f76","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"version":"2025.4.0-1-g37f21b1","token":"cd0b4b3a733644fc843ef0b185f98241"}' crossorigin="anonymous"></script>
    @yield('content_menu')
</body>
</html>

