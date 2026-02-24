<?php
$db = new SQLite3('database.db');

/* DELETE */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $image = $db->querySingle("SELECT image FROM leaders WHERE id=$id");
    if ($image && file_exists("uploads/".$image)) {
        unlink("uploads/".$image);
    }

    $db->exec("DELETE FROM leaders WHERE id=$id");
    header("Location: admin.php");
    exit();
}

/* LOAD FOR EDIT */
$editMode = false;
if (isset($_GET['edit'])) {
    $editMode = true;
    $id = intval($_GET['edit']);
    $row = $db->querySingle("SELECT * FROM leaders WHERE id=$id", true);
}

/* SAVE */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $caption = $_POST['caption'];
    $date = date("Y-m-d");

    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);

        if (!empty($_FILES["image"]["name"])) {
            $imageName = time()."_".basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/".$imageName);
            $db->exec("UPDATE leaders SET caption='$caption', image='$imageName' WHERE id=$id");
        } else {
            $db->exec("UPDATE leaders SET caption='$caption' WHERE id=$id");
        }

    } else {
        $imageName = time()."_".basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/".$imageName);

        $stmt = $db->prepare("INSERT INTO leaders (caption,image,date) VALUES (:caption,:image,:date)");
        $stmt->bindValue(':caption',$caption,SQLITE3_TEXT);
        $stmt->bindValue(':image',$imageName,SQLITE3_TEXT);
        $stmt->bindValue(':date',$date,SQLITE3_TEXT);
        $stmt->execute();
    }

    header("Location: admin.php");
    exit();
}

$result = $db->query("SELECT * FROM leaders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { margin:0; font-family:'Segoe UI'; background:#f4f6f9; }

header {
    background:#2c3e50;
    color:white;
    padding:20px;
    text-align:center;
}

.container {
    max-width:1000px;
    margin:30px auto;
}

form {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    margin-bottom:30px;
}

input, button {
    width:100%;
    padding:10px;
    margin-bottom:12px;
    border-radius:6px;
    border:1px solid #ccc;
}

button {
    background:#2c3e50;
    color:white;
    border:none;
    cursor:pointer;
}

button:hover { background:#1a252f; }

table {
    width:100%;
    background:white;
    border-collapse:collapse;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

th, td {
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eee;
}

th {
    background:#34495e;
    color:white;
}

.actions a {
    padding:5px 10px;
    border-radius:5px;
    color:white;
    text-decoration:none;
    font-size:14px;
}

.edit { background:#3498db; }
.delete { background:#e74c3c; }

.edit:hover { background:#2980b9; }
.delete:hover { background:#c0392b; }

.back { text-align:center; margin-top:20px; }

</style>
</head>
<body>

<header>
<h1>Admin Panel</h1>
</header>

<div class="container">

<form method="POST" enctype="multipart/form-data">
<?php if($editMode) { ?>
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<?php } ?>

<input type="text" name="caption"
value="<?php echo $editMode ? htmlspecialchars($row['caption']) : ''; ?>"
placeholder="Enter Notice Title" required>

<input type="file" name="image" accept="image/*">
<small><?php if($editMode) echo "Leave empty to keep current image"; ?></small>

<button type="submit">
<?php echo $editMode ? "Update Notice" : "Add Notice"; ?>
</button>
</form>

<table>
<tr>
<th>Image</th>
<th>Title</th>
<th>Date</th>
<th>Actions</th>
</tr>

<?php while($row = $result->fetchArray()) { ?>
<tr>
<td><img src="uploads/<?php echo $row['image']; ?>" width="60"></td>
<td><?php echo htmlspecialchars($row['caption']); ?></td>
<td><?php echo $row['date']; ?></td>
<td class="actions">
<a class="edit" href="admin.php?edit=<?php echo $row['id']; ?>">Edit</a>
<a class="delete" href="admin.php?delete=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this notice?')">Delete</a>
</td>
</tr>
<?php } ?>

</table>

<div class="back">
<a href="Notices.php">← Back to Notices</a>
</div>

</div>

</body>
</html>