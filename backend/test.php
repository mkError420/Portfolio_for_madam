<?php
// Simple test file to check if PHP is working
echo "PHP is working!";
echo "<br>";
echo "Current directory: " . __DIR__;
echo "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Not set';
?>
