<?php
$conn = new mysqli('localhost', 'root', '', 'bolsa_trabajo_js');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$schema = "";
foreach ($tables as $table) {
    $res = $conn->query("SHOW CREATE TABLE `$table`");
    if ($row = $res->fetch_row()) {
        $schema .= $row[1] . ";\n\n";
    }
}
file_put_contents('schema_dump.sql', $schema);
echo "Schema dumped to schema_dump.sql";
?>
