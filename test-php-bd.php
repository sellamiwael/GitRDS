<?php
define('DB_SERVER', 'database-1.cukrim5mtydt.us-east-1.rds.amazonaws.com');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', 'test2023');
define('DB_DATABASE', 'db1');
?>

<html>
<head>
    <title>Test Page PHP</title>
</head>
<body>

<h1>Employee Information</h1>

<?php
// Fonction pour vérifier l'existence d'une table
function tableExists($tableName, $connection, $dbName) 
{
    $tableName = mysqli_real_escape_string($connection, $tableName);
    $dbName = mysqli_real_escape_string($connection, $dbName);
    $checkTable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$tableName' AND TABLE_SCHEMA = '$dbName'");

    return mysqli_num_rows($checkTable) > 0;
}

// Fonction pour créer la table EMPLOYEES si elle n'existe pas
function verifyEmployeesTable($connection, $dbName) 
{
    if (!tableExists("EMPLOYEES", $connection, $dbName)) 
    {
        $query = "CREATE TABLE EMPLOYEES (
                    ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    NAME VARCHAR(45),
                    ADDRESS VARCHAR(90)
                    )";
                    
        if (!mysqli_query($connection, $query)) 
        {
            echo("<p>Error creating table.</p>");
        }
    }
}

// Fonction pour ajouter un employé à la table
function addEmployee($connection, $name, $address) 
{
    $n = mysqli_real_escape_string($connection, $name);
    $a = mysqli_real_escape_string($connection, $address);
    $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";
    if (!mysqli_query($connection, $query)) 
    {
        echo("<p>Error adding employee data.</p>");
    }        
}

// Connexion à MySQL et sélection de la base de données
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if (mysqli_connect_errno()) 
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$database = mysqli_select_db($connection, DB_DATABASE);

// Assurer que la table EMPLOYEES existe
verifyEmployeesTable($connection, DB_DATABASE);

// Traitement du formulaire d'ajout d'un employé
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $employeeName = htmlentities($_POST['NAME']);
    $employeeAddress = htmlentities($_POST['ADDRESS']);

    if (strlen($employeeName) > 0 || strlen($employeeAddress) > 0) 
    {
        addEmployee($connection, $employeeName, $employeeAddress);
    }
}
?>

<!-- Formulaire d'ajout d'un employé -->
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
    <table border="0">
        <tr>
            <td>NAME</td>
            <td>ADDRESS</td>
        </tr>
        <tr>
            <td>
                <input type="text" name="NAME" maxlength="45" size="30" />
            </td>
            <td>
                <input type="text" name="ADDRESS" maxlength="90" size="60" />
            </td>
            <td>
                <input type="submit" value="Add Data" />
            </td>
        </tr>
    </table>
</form>
    
<!-- Affichage des données de la table -->
<table border="1" cellpadding="2" cellspacing="2">
    <tr>
        <td>ID</td>
        <td>NAME</td>
        <td>ADDRESS</td>
    </tr>

    <?php
    $result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

    while ($queryData = mysqli_fetch_row($result)) 
    {
        echo "<tr>";
        echo "<td>", $queryData[0], "</td>",
             "<td>", $queryData[1], "</td>",
             "<td>", $queryData[2], "</td>";
        echo "</tr>";
    }
    ?>

</table>

<!-- Nettoyage -->
<?php
mysqli_free_result($result);
mysqli_close($connection);
?>

</body>
</html>
