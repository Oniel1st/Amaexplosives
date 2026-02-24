<?php
$db = new SQLite3('database.db');
$result = $db->query("SELECT * FROM leaders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Notices</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f4f6f9;
}

header {
    background: linear-gradient(135deg,#2c3e50,#34495e);
    color:white;
    padding:20px;
    text-align:center;
}

#gallery {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    padding:20px;
    max-width:1200px;
    margin:auto;
}

.card {
    background:white;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

.card img {
    width:100%;
    height:220px;
    object-fit:cover;
}

.card-content {
    padding:15px;
    text-align:center;
}

.date {
    font-size:14px;
    color:#777;
    margin-top:8px;
}
</style>
</head>
<body>

<header>
<h1>Notices Board</h1>
<a href="admin.php" style="color:white;">Admin Login</a>
</header>

<section id="gallery">
<?php while($row = $result->fetchArray()) { ?>
    <div class="card">
        <img src="uploads/<?php echo $row['image']; ?>">
        <div class="card-content">
            <h3><?php echo htmlspecialchars($row['caption']); ?></h3>
            <div class="date">Posted on <?php echo $row['date']; ?></div>
        </div>
    </div>
<?php } ?>
</section>

</body>
</html>