<?php

require_once $_SERVER["DOCUMENT_ROOT"] ."/Database/DBConx.php";

//get the top parent categories
$sql = "SELECT category.Id, category.Name FROM category 
        WHERE id NOT IN (SELECT catetory_relations.categoryId FROM catetory_relations) 
        AND category.Disabled=0";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $parent_categories = $result->fetch_all(MYSQLI_ASSOC);
}

//will return total items of a category
function getItemsOfCategory($categoryId)
{
  require $_SERVER["DOCUMENT_ROOT"] ."/Database/DBConx.php";
  $itemCount = 0;

  $sql = "SELECT COUNT(Item_category_relations.ItemNumber) AS item_count
          FROM Item_category_relations WHERE categoryId=$categoryId";
  
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
      $itemcounted = $result->fetch_assoc();
  }
  $itemCount += $itemcounted['item_count'];

  //check if the category has any child and get item counts for them
  $sql = "SELECT categoryId FROM catetory_relations WHERE ParentcategoryId=$categoryId";
  
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
      $childCategories = $result->fetch_all(MYSQLI_ASSOC);

      foreach ($childCategories as $childCategory) {
        $childCategoryItemsCount = getItemsOfCategory($childCategory['categoryId']);
        $itemCount += $childCategoryItemsCount;
      }
  }
  return $itemCount;
}

function printChildCategories($categoryId)
{
  require $_SERVER["DOCUMENT_ROOT"] ."/Database/DBConx.php";
  //get childs of the category
  $sql = "SELECT category.Id, category.Name FROM category
          JOIN catetory_relations ON catetory_relations.categoryId=category.Id 
          WHERE ParentcategoryId=$categoryId";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $childCategories = $result->fetch_all(MYSQLI_ASSOC);

    echo "<ul>";

    foreach ($childCategories as $childCategory) {
        echo "<li>";
        //print category name and item count
        echo $childCategory['Name']. " (";
        echo getItemsOfCategory($childCategory['Id']) .")";

        //print child categories of this category
        echo printChildCategories($childCategory['Id']);

        echo "</li>";
    }
    echo "</ul>";
  }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Brain Station 23 - Task 2</title>
  
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">

</head>
<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">

  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>                        
        </button>
        <a class="navbar-brand" href="/">Brain Station 23</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="/">Task 1</a></li>
          <li><a href="/task2.php" active>Task 2</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="jumbotron text-center">
    <h1>Task 2</h1>
  </div>

  <!-- Container (About Section) -->
  <div id="about" class="container-fluid">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="box">
          <div class="box-body">
            <ul>
              <?php
              foreach ($parent_categories as $parent_category) {
                $totalItems = 0;
                $itemcCountOfCategory = getItemsOfCategory($parent_category['Id']);
                ?>
                <li>
                  <?php echo $parent_category['Name'] ?> (<?php echo $itemcCountOfCategory; ?>)
                  <!-- print child categories -->
                  <?php echo printChildCategories($parent_category['Id']); ?>

                </li>
                <?
              }
              ?>
            </ul>
          </div>
          <!-- /.box-body -->
        </div>
      </div>
    </div>
  </div>
  <br><br>
  <footer class="container-fluid text-center">
    <p>Assesment Task Done By - <a href="http://rrrlab.com">Md. Rayhanur Rahaman Rubel</a></p>
    <p>Mobile: 01723858781</p>
    <p>Email: rayhan.rubel@icloud.com</p>
    <p>Alternative Email: rayhan.rrrbd@gmail.com</p>
  </footer>
</body>
</html>
