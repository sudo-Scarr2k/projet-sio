<?php
// register.php
include("includes/functions/config.php");

$message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Define variables and initialize with empty values
    $name = $email = $username = $password = "";

    // Processing form data when form is submitted
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);

    // Prepare an insert statement
    $sql = "INSERT INTO users (name, email, username, password) VALUES (:name, :email, :username, :password)";

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);
        // Bind parameters to the prepared statement
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to login page
            header("location: login.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close statement
    unset($stmt);
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // login.php
    session_start();

    include("includes/config.php");

    $message = '';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
    } else {
        $message = 'Mauvais identifiants';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & register</title>
    <link rel="stylesheet" href="assets/style1.css">
    <link href="https://fonts.googleapis.com/css2?family=Honk" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo">
        <img src="assets/logo.png"><div class="p-logo1">Cloud</div><div class="p-logo2">Zone</div></img>
    </div>
    <div class="navigation">
        <nav>
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Services</a>
            <a href="#">Contact</a>
            <button class="btnLogin-popup">Login</button>
        </nav>
    </div>
</header>
<main role="main">
    <div class="container">
        <div class="login-box">
    </div>
    <div class="content">
        <div class="main-content">
            <?php
            // Inclure le fichier de configuration de la base de données
            include_once 'includes/functions/connectdb_im.php'; // Assurez-vous de mettre le bon chemin vers votre fichier de fonctions

            // Nombre total de produits
            $totalProducts = 20;

            // Nombre de produits par page
            $productsPerPage = 9;

            // Calculer le nombre total de pages
            $totalPages = ceil($totalProducts / $productsPerPage);

            // Récupérer le numéro de la page actuelle
            $currentpage = isset($_GET['page']) ? $_GET['page'] : 1;

            // Calculer l'index de départ pour la requête SQL
            $start = ($currentpage - 1) * $productsPerPage;

            // Connexion à la base de données
            $pdo = connectdbim();

            // Code HTML pour afficher les produits
            echo '<table>';
            for ($i = 0; $i < $productsPerPage; $i++) {
                $index = $start + $i;
                // Si l'index dépasse le nombre total de produits, sortir de la boucle
                if ($index >= $totalProducts) {
                    break;
                }
                // Récupérer l'identifiant de l'image dans la base de données en fonction de son nom
                $imageName = 'product' . ($index + 1) . '.jpg'; // Supposons que les noms des images sont dans ce format
                $stmt = $pdo->prepare("SELECT id FROM images WHERE name = ?");
                $stmt->execute([$imageName]);
                $imageRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $imageId = $imageRow ? $imageRow['id'] : ''; // Si aucune image trouvée, utiliser une chaîne vide
                
                // Chemin de l'image
                $imagePath = 'assets/images/' . $imageName;

                // Code HTML pour afficher un produit
                echo '<td>
                        <div class="wrapper-container">
                            <div class="product-container">
                                <div class="product-card">
                                    <div class="product-image">
                                        <span class=""></span>
                                        <img src="' . $imagePath . '" class="product-thumb" alt="Product Image">
                                        <button class="card-btn">Add to Cart</button>
                                    </div>
                                    <div class="product-info">
                                        <h2>Lavasan Orange Jubilee</h2>
                                        <p class="product-short-des">Lorem ipsum.</p>
                                        <span class="price">$20</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>';
                // Si nous avons atteint la fin de la rangée, commençons une nouvelle rangée
                if (($i + 1) % 3 === 0) {
                    echo '</tr><tr>';
                }
            }
            echo '</table>';

            // Afficher les liens de pagination
            echo '<div class="pagination">';
            for ($i = 1; $i <= $totalPages; $i++) {
                // Si c'est la page actuelle, ne pas inclure de lien
                if ($i == $currentpage) {
                    echo '<span class="active">' . $i . '</span>';
                } else {
                    echo '<a href="?page=' . $i . '">' . $i . '</a>';
                }
            }
            echo '</div>';
            ?>
        </div>
    </div>
    <div class="login-wrap">
        <div class="wrapper">
            <span class="icon-close">
                <ion-icon name="close-outline"></ion-icon>
            </span>
            <div class="form-box-login">
                <h2>Login</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-box">
                        <span class="icon"><ion-icon name="at-outline"></ion-icon></span>
                        <input type="text" name="username" required>
                        <label>Username</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <button type="submit" class="btn" name="login">Login</button>
                    <div class="login-register">
                        <p>Don't have an account?<a href="#" class="register-link">Register</a></p>
                    </div>
                </form>
            </div>
            <div class="form-box-register">
                <h2>Register</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-box">
                        <span class="icon"><ion-icon name="person"></ion-icon></span>
                        <input type="text" name="name" required>
                        <label>Full Name</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail"></ion-icon></span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="at-outline"></ion-icon></span>
                        <input type="text" name="username" required>
                        <label>Username</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <button type="submit" class="btn" name="register">Register</button>
                    <div class="register-login">
                        <p>Already have an account?<a href="#" class="login-link">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="includes/js/script.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>