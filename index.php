<?php
 
//header("Content-Type:text/html;charset='utf-8'");
 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1></h1>
</body>
</html>

<form action="upload.php" method="post" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" value="Download">
</form>
<?php
	$dir = 'image_fullscreen/'; // Папка с изображениями
	$copydir = 'image_preview/';
	$files = scandir($dir);
	for ($i = 0; $i < count($files); $i++) { 
		if (($files[$i] != ".") && ($files[$i] != "..")) { // Текущий каталог и родительский пропускаем
			$path = $dir.$files[$i];
			$copypath = $copydir.$files[$i];
			copy($path, $copypath);
		}
	}
?>
<?php
    $dir = 'image_preview/';
    $files = scandir($dir);
    for ($i = 0; $i < count($files); $i++) {
    	if (($files[$i] != ".") && ($files[$i] != "..")) { // Текущий каталог и родительский пропускаем
    		$path = $dir.$files[$i];
    		makeThumbnail($path, $path, 200, 200, 100);
    	}
    }
?>
<script type="text/javascript">
  function swipe() {
   var largeImage = document.getElementById('largeImage');
   largeImage.style.display = 'block';
   largeImage.style.width=200+"px";
   largeImage.style.height=200+"px";
   var url=largeImage.getAttribute('src');
   window.open(url,'Image','width=largeImage.stylewidth,height=largeImage.style.height,resizable=1');
}
</script>
<?php
function makeThumbnail($sourcefile, $endfile, $thumbwidth, $thumbheight, $quality) {
    // Takes the sourcefile (path/to/image.jpg) and makes a thumbnail from it
    // and places it at endfile (path/to/thumb.jpg).

    // Load image and get image size.
    $img = imagecreatefromjpeg($sourcefile);
    $width = imagesx( $img );
    $height = imagesy( $img );

    if ($width > $height) {
        $newwidth = $thumbwidth;
        $divisor = $width / $thumbwidth;
        $newheight = floor( $height / $divisor);
    } else {
        $newheight = $thumbheight;
        $divisor = $height / $thumbheight;
        $newwidth = floor( $width / $divisor );
    }

    // Create a new temporary image.
    $tmpimg = imagecreatetruecolor( $newwidth, $newheight );

    // Copy and resize old image into new image.
    imagecopyresampled( $tmpimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

    // Save thumbnail into a file.
    imagejpeg( $tmpimg, $endfile, $quality);

    // release the memory
    imagedestroy($tmpimg);
    imagedestroy($img);
}
?>
<?php
function watermark_image($oldimage_name, $new_image_name){
	// получаем имя изображения, используемого в качестве водяного знака 
	global $image_path;
	// получаем размеры исходного изображения
	list($owidth,$oheight) = getimagesize($oldimage_name);
	// задаем размеры для выходного изображения 
	$width = $owidth;
	$height = $oheight; 
	// создаем выходное изображение размерами, указанными выше
	$im = imagecreatetruecolor($width, $height);
	$img_src = imagecreatefromjpeg($oldimage_name);
	// наложение на выходное изображение, исходного
	imagecopyresampled($im, $img_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
	$watermark = imagecreatefrompng($image_path);
	// получаем размеры водяного знака
	list($w_width, $w_height) = getimagesize($image_path);
	// определяем позицию расположения водяного знака 
	$pos_x = $width - $w_width; 
	$pos_y = $height - $w_height;
	// накладываем водяной знак
	imagecopy($im, $watermark, $pos_x, $pos_y, 0, 0, $w_width, $w_height);
	// сохраняем выходное изображение, уже с водяным знаком в формате jpg и качеством 100
	imagejpeg($im, $new_image_name, 100);
	// уничтожаем изображения
	imagedestroy($im);
	return true;
}
?>
<?php
    $dir = 'image_fullscreen/';
    $dir1 = 'image_photo_stamp/';
    $files = scandir($dir);
    for ($i = 0; $i < count($files); $i++) {
    	if (($files[$i] != ".") && ($files[$i] != "..")) { // Текущий каталог и родительский пропускаем
    		$path = $dir.$files[$i];
    		$path1 = $dir1.$files[$i];
    		$image_path = 'watermark1.png';
    		watermark_image($path, $path1);
    	}
    }
?>
<?php
  $dir = 'image_photo_stamp/'; // Папка с изображениями
  $cols = 5; // Количество столбцов в будущей таблице с картинками
  $files = scandir($dir); // Берём всё содержимое директории
  echo "<table>"; // Начинаем таблицу
  $k = 0; // Вспомогательный счётчик для перехода на новые строки
  for ($i = 0; $i < count($files); $i++) { // Перебираем все файлы
    if (($files[$i] != ".") && ($files[$i] != "..")) { // Текущий каталог и родительский пропускаем
      if ($k % $cols == 0) echo "<tr>"; // Добавляем новую строку
      echo "<td>"; // Начинаем столбец
      $path = $dir.$files[$i]; // Получаем путь к картинке
      echo "<a href='$path'>"; // Делаем ссылку на картинку
      echo "<img src='$path', width = '200', height = '200' onclick=swipe();/>"; // Вывод превью картинки
      echo "</a>"; // Закрываем ссылку
      echo "</td>"; // Закрываем столбец
      /* Закрываем строку, если необходимое количество было выведено, либо данная итерация последняя */
      if ((($k + 1) % $cols == 0) || (($i + 1) == count($files))) echo "</tr>";
      $k++; // Увеличиваем вспомогательный счётчик
    }
  }
  echo "</table>"; // Закрываем таблицу
?>