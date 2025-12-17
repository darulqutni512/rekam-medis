<?php
$h='127.0.0.1'; $u='root'; $p='';
$c = mysqli_connect($h,$u,$p);
if(!$c){ echo "connect fail: ".mysqli_connect_error()."\n"; exit(1);} 
$sql = file_get_contents(__DIR__.'/create_db.sql');
$parts = preg_split('/;\\s*(?:\\r?\\n|$)/', $sql);
$i=0;
foreach($parts as $stmt){
  $i++;
  $stmt = trim($stmt);
  if(!$stmt) continue;
  echo "stmt $i: ".substr($stmt,0,120)."\n";
  $res = @mysqli_query($c, $stmt);
  if(!$res){
    echo "ERR stmt $i: ".mysqli_errno($c)." ".mysqli_error($c)."\n";
  } else {
    echo "OK stmt $i\n";
  }
}
echo "done\n";
