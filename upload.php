<?php
require_once 'assets/php/connections/pdo.php';
require_once 'assets/php/html_strings/nav_bar.php';
require_once 'assets/php/utils.php';


// session_start();

// retrieve access contraints from db
$stmt = $conn->prepare('SELECT id,name,"desc" FROM accesses');
$stmt->execute();
$access_constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);

$contraint_hld = "<option value=':constraint'>:constraint</option>";
$constraints = "";

// if access constraints are not empty, create option with no value and append to constraints
if (!empty($access_constraints)) {
    $constraints .= "<option value=''>Select Access Level</option>";

    // create an associative array of id and name
    $constraints_array = array_column($access_constraints, 'id', 'name');

}


foreach ($access_constraints as $constraint) {
    $constraints .= str_replace(':constraint', $constraint['name'], $contraint_hld);
}

// if session name is set redirect to index.php
if (!isset($_SESSION['name'])) {
    header('Location: index.php');
    return;
}

// if post echo post submit with print_r and return
// print_r($_POST);
// return;

$nav = setActiveNav('upload', $nav);

// if submit value is cancel, redirect to index.php
if (isset($_POST['submit']) && $_POST['submit'] == 'cancel') {
    header('Location: index.php');
    return;
}


if (isset($_POST['submit']) && $_POST['submit'] == 'upload') {

    // print_r($_FILES['videoFile']);
    // print_r("triggered");
    // return;

    // save thumbnail in data/thumbnails
    // save video in data/videos
    // save tags in db
    // save video details in db

    // move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'data/thumbnails/' . $_FILES['thumbnail']['name']);
    // move_uploaded_file($_FILES['videoFile']['tmp_name'], 'data/videos/' . $_FILES['videoFile']['name']);

    

    // check if all fields are filled string length > 1
    if ((strlen($_POST['videoName']) < 1 || strlen($_POST['access']) < 1 || strlen($_POST['owner']) < 1 || strlen($_POST['tags']) < 1) ){
        $_SESSION['error'] = "All fields are required";
        header("Location: upload.php");
        return;
    }

    // check if files are uploaded
    if (!isset($_FILES['thumbnail']) || !isset($_FILES['videoFile'])) {
        $_SESSION['error'] = "Thumbnail and video file are required";
        header("Location: upload.php");
        return;
    }

    // if owner is not in user table return an error
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute(array(':username' => $_POST['owner']));
    $owner_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    if (!$owner_id) {
        $_SESSION['error'] = "Owner does not exist";
        header("Location: upload.php");
        return;
    }

    // if file sizes exceed 20M return an error
    if ($_FILES['thumbnail']['size'] > 20000000 || $_FILES['videoFile']['size'] > 20000000) {
        $_SESSION['error'] = "File size exceeds 20MB";
        header("Location: upload.php");
        return;
    }

    // if the total file sizes exceed 20M return an error
    if ($_FILES['thumbnail']['size'] + $_FILES['videoFile']['size'] > 20000000) {
        $_SESSION['error'] = "Total file size exceeds 20MB";
        header("Location: upload.php");
        return;
    }

    // check if data directory exists ,if directory doesnt exits create directory for data and child directories for thumbnails and videos
    if (!file_exists('data')) {
        mkdir('data');
        mkdir('data/thumbnails');
        mkdir('data/videos');
    }
    if (!file_exists('data/thumbnails')) {
        mkdir('data/thumbnails');
    }
    if (!file_exists('data/videos')) {
        mkdir('data/videos');
    }

    // retrive file names and save them in their respective directories
    $thumbnail_file_name = $_FILES['thumbnail']['name'];
    $video_file_name = $_FILES['videoFile']['name'];

    // resize image to get dimensions fit for a thumbnail and save
    $thumbnail = imagecreatefromstring(file_get_contents($_FILES['thumbnail']['tmp_name']));
    $thumbnail = imagescale($thumbnail, 320, 180);
    imagejpeg($thumbnail, 'data/thumbnails/' . $thumbnail_file_name);




    $thumbnail_file_path = 'data/thumbnails/' . $thumbnail_file_name;
    $video_file_path = 'data/videos/' . $video_file_name;

    // save files
    // move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'data/thumbnails/' . $_FILES['thumbnail']['name']);
    move_uploaded_file($_FILES['videoFile']['tmp_name'], 'data/videos/' . $_FILES['videoFile']['name']);






    // print_r('tirgered');
    // return;

    // check if tags are not in db already and add them to db
    // convert tags into lowercase
    $_POST['tags'] = strtolower($_POST['tags']);
    $tags = explode(",", $_POST['tags']);
    $tags = array_unique($tags);

    // retrive tags from db
    $stmt = $conn->prepare('SELECT id, tag FROM tags');
    $stmt->execute();
    $tags_in_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // create an associative array of name and id
    $tags_in_db = array_column($tags_in_db, 'tag', 'id');

    // for each entered tag if not in tags array, add to db and update array with tag and id
    foreach ($tags as $tag) {
        if (strlen($tag)>0 && !in_array($tag, $tags_in_db)) {
            $stmt = $conn->prepare('INSERT INTO tags (tag) VALUES (:tag)');
            $stmt->execute(array(':tag' => $tag));
            $tags_in_db[$conn->lastInsertId()] = $tag;
        }
    }

    // remove all entries of tags in array which are not in the entries from the user
    $tags_in_db = array_intersect($tags_in_db, $tags);

    // retrive the id of the owner of the video
    // $stmt = $conn->prepare('SELECT id FROM users WHERE username = :username');
    // $stmt->execute(array(':username' => $_POST['owner']));
    // $owner_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

    // insert thumbnail into db and retrive the id
    $stmt = $conn->prepare('INSERT INTO thumbnails (name, path) VALUES (:name, :path)');
    $stmt->execute(array(':name' => $thumbnail_file_name, ':path' => $thumbnail_file_path));
    $thumbnail_id = $conn->lastInsertId();


    // inset data into db and retrieve the id
    $stmt = $conn->prepare('INSERT INTO videos (uploaded_by, owned_by, uploaded_at, access, path, name, thumbnail) VALUES (:uploaded_by, :owned_by, :uploaded_at, :access, :path, :name, :thumbnail)');
    $stmt->execute(array(
        ':uploaded_by' => $_SESSION['user_id'],
        ':owned_by' => $owner_id,
        ':uploaded_at' => date('Y-m-d H:i:s'),
        ':access' => $constraints_array[$_POST['access']],
        ':path' => $video_file_path,
        ':name' => $_POST['videoName'],
        ':thumbnail' => $thumbnail_id
    ));
    $video_id = $conn->lastInsertId();

    // insert video tags into db
    foreach ($tags_in_db as $tag_id => $tag_name) {
        $stmt = $conn->prepare('INSERT INTO video_tags (video_id, tag_id) VALUES (:video_id, :tag_id)');
        $stmt->execute(array(':video_id' => $video_id, ':tag_id' => $tag_id));
    }

    $_SESSION['success'] = "Video uploaded successfully";
    header("Location: upload.php");
    return;


    // print_r($_POST);
    // return;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Upload Form</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        /* Center the row vertically within its parent container */
        .vertical-center {
            min-height: 100%; /* Fallback for browsers do NOT support vh unit */
            min-height: 100vh; /* These two lines are counted as one :-)       */
            display: flex;
            align-items: center;
        }

        .user-initials {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            line-height: 40px;
        }  
    </style>
</head>
<body>
<?php echo $nav; ?>
    <div class="container">
        <div class="row vertical-center">

            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <h1>Video Upload Form</h1>
                <form  method="post" enctype="multipart/form-data">

                <div class="mt-3">
                    <?php
                        if (isset($_SESSION['error'])) {
                            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
                            unset($_SESSION['error']);
                        }
                        if (isset($_SESSION['success'])) {
                            echo ('<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n");
                            unset($_SESSION['success']);
                        }
                    ?>
                </div>
                    <!-- Video Name -->
                    <div class="form-group">
                        <label for="videoName">Video Name:</label>
                        <input type="text" class="form-control" id="videoName" name="videoName">
                    </div>

                    <!-- Access Level -->
                    <div class="form-group">
                        <label for="access">Access Level:</label>
                        <select class="form-control" id="access" name="access">
                            <?php echo $constraints; ?>
                        </select>
                    </div>

                    <!-- Owner -->
                    <div class="form-group">
                        <label for="owner">Owner:</label>
                        <input type="text" class="form-control" id="owner" name="owner" placeholder="Enter a username of owner of video">
                    </div>

                    <!-- Thumbnail Upload -->
                    <div class="form-group">
                        <label for="thumbnail">Thumbnail:</label>
                        <input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/*">
                    </div>

                    <!-- Thumbnail Preview -->
                    <div class="form-group" id="thumbnail-preview-container" style="display: none;">
                        <label>Thumbnail Preview:</label>
                        <img id="thumbnail-preview" src="#" alt="Thumbnail Preview" style="max-width: 100%;">
                    </div>

                    <!-- Video Upload -->
                    <div class="form-group">
                        <label for="videoFile">Video File:</label>
                        <input type="file" class="form-control-file" id="videoFile" name="videoFile" accept="video/*">
                    </div>

                    <!-- Tags -->
                    <div class="form-group">
                        <label for="tags_transit">Tags (press Enter to add tags):</label>
                        <div class="mb-2" id="tags-container">
                            <!-- Tags will be added here as labels -->
                        </div>
                        <input type="text" class="form-control" id="tags_transit" placeholder="Enter tags and press Enter">
                    </div>

                    <div class="form-group" style="display:none;">
                        <input type="text" class="form-control" id="tags" name="tags">
                    </div>
                    
                    <!--two side by side buttons, one to submit and one to cancel -->
                    <div class="form-group">
                        <div class="container">
                            <div class="row">
                                <button name="submit" value="upload" type="submit" class="btn btn-lg btn-primary col-sm-4 mt-3">Upload Video</button>
                                <div class="col-sm-4"></div>
                                <button value="cancel" name="submit" type="submit" class="btn btn-lg btn-danger col-sm-4 mt-3">CANCEL</button>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>

            <div class="col-sm-3">

        </div>
    </div>
        

    <!-- JavaScript to handle thumbnail preview and prevent form submission on Enter key -->
    <script>
        const thumbnailInput = document.getElementById("thumbnail");
        const thumbnailPreviewContainer = document.getElementById("thumbnail-preview-container");
        const thumbnailPreview = document.getElementById("thumbnail-preview");

        thumbnailInput.addEventListener("change", function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    thumbnailPreview.src = e.target.result;
                    thumbnailPreviewContainer.style.display = "block";
                };

                reader.readAsDataURL(this.files[0]);
            } else {
                thumbnailPreviewContainer.style.display = "none";
            }
        });

        const tagsContainer = document.getElementById("tags-container");
        const tagsInput = document.getElementById("tags_transit");
        const tagsHiddenInput = document.getElementById("tags");

        tagsInput.addEventListener("keydown", function(event) {
            if (event.key === "Enter" && this.value.trim() !== "") {
                const tagLabel = document.createElement("span");
                tagLabel.className = "badge badge-primary mr-1";
                tagLabel.textContent = this.value.trim();

                tagsHiddenInput.value += this.value.trim() + ",";
                tagsContainer.appendChild(tagLabel);
                this.value = ""; // Clear the input
                event.preventDefault(); // Prevent form submission on Enter key
            }
        });
    </script>

    <!-- Add Bootstrap JS and jQuery scripts here -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
