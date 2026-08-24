<?php
$conn = new mysqli("localhost", "root", "", "ideal_law");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
