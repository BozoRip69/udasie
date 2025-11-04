<?php
$conn = new mysqli('localhost', 'root', '', 'users'); // dostosuj nazwę bazy

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        die("Hasła nie są takie same. <a href='rejestracja.html'>Wróć</a>");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // sprawdź, czy email istnieje
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Ten e-mail jest już zarejestrowany. <a href='login.html'>Zaloguj się</a>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $username = explode('@', $email)[0]; // np. jan@example.com → jan
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        if ($stmt->execute()) {
            echo "✅ Rejestracja udana! <a href='login.html'>Zaloguj się</a>";
        } else {
            echo "Błąd zapisu: " . $stmt->error;
        }
    }
}
?>
