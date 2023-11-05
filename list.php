<?php
session_start();

require_once 'database.php';

if (!isset($_SESSION['logged_id'])) {

	if (isset($_POST['login'])) {

		$login = filter_input(INPUT_POST, 'login');
		$password = filter_input(INPUT_POST, 'pass');

		//echo $login . " " .$password;

		$userQuery = $db->prepare('SELECT id, password FROM admins WHERE login = :login');
		$userQuery->bindValue(':login', $login, PDO::PARAM_STR);
		$userQuery->execute();

		//echo $userQuery->rowCount();

		$user = $userQuery->fetch();

		// echo $user['id'] . " " . $user['password'];

		if ($user && password_verify($password, $user['password'])) {
			$_SESSION['logged_id'] = $user['id'];
			unset($_SESSION['bad_attempt']);
		} else {
			$_SESSION['bad_attempt'] = true;
			header('Location: admin.php');
			exit();
		}

	} else {

		header('Location: admin.php');
		exit();
	}
}

$usersQuery = $db->query('SELECT * FROM uzytkownicy');
$users = $usersQuery->fetchAll();

//print_r($users);

?>
<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<title>Panel administracyjny</title>
	<meta name="description" content="Używanie PDO - odczyt z bazy MySQL">
	<meta name="keywords" content="php, kurs, PDO, połączenie, MySQL">
	<meta http-equiv="X-Ua-Compatible" content="IE=edge">

	<!-- <link rel="stylesheet" href="main.css"> -->
	<link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext"
		rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
	<![endif]-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
</head>

<body class="bg-info">

	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 bg-light rounded my-2 py-2">
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>User</th>
							<th>email</th>
							<th>drewno</th>
							<th>kamien</th>
							<th>zboze</th>
							<th>dniPremium</th>
							<th>image_url</th>
						</tr>
					</thead>
					<tbody>
						<img src="" alt="">
						<?php
						foreach ($users as $user) {
						?>
						<tr>
							<td><?=$user['id'] ?></td>
							<td><?=$user['user'] ?></td>
							<td><?=$user['email'] ?></td>
							<td><?=$user['drewno'] ?></td>
							<td><?=$user['kamien'] ?></td>
							<td><?=$user['zboze'] ?></td>
							<td><?=$user['dnipremium'] ?></td>
							<td><img src="uploads/<?= $user['image_url'] ?>" alt="User Image" width="50" height="50"></td>
						</tr>
						<?php
						} ?>
					</tbody>
				</table>

			</div>
		</div>

	</div>
	<p><a href="logout.php">Wyloguj się!</a></p>
	<!-- <script>
		var usersData = <?php echo json_encode($users); ?>;

	</script>
	<script src="list.js"></script> -->
	<script type="text/javascript">
		$(document).ready(function () {
			$('table').DataTable();
		});
	</script>
</body>

</html>