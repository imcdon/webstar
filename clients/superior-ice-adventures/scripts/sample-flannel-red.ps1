Add-Type -AssemblyName System.Drawing
$path = "C:\xampp\htdocs\superior-ice-adventures\assets\img\textures\flannel-red-black.png"
$img = [System.Drawing.Bitmap]::FromFile($path)
$sumR = 0L; $sumG = 0L; $sumB = 0L; $n = 0
$maxR = 0; $maxG = 0; $maxB = 0
for ($x = 0; $x -lt $img.Width; $x += 4) {
  for ($y = 0; $y -lt $img.Height; $y += 4) {
    $c = $img.GetPixel($x, $y)
    if ($c.R -ge 110 -and $c.G -lt 80 -and $c.B -lt 80 -and ($c.R - $c.G) -gt 40) {
      $sumR += $c.R; $sumG += $c.G; $sumB += $c.B; $n++
      if ($c.R -gt $maxR) { $maxR = $c.R; $maxG = $c.G; $maxB = $c.B }
    }
  }
}
$img.Dispose()
$ar = [int]($sumR / [Math]::Max($n,1))
$ag = [int]($sumG / [Math]::Max($n,1))
$ab = [int]($sumB / [Math]::Max($n,1))
"n=$n"
"avg=#{0:X2}{1:X2}{2:X2}" -f $ar,$ag,$ab
"avg-rgb=$ar,$ag,$ab"
"max=#{0:X2}{1:X2}{2:X2}" -f $maxR,$maxG,$maxB
"max-rgb=$maxR,$maxG,$maxB"
Out-File -FilePath "C:\xampp\htdocs\superior-ice-adventures\scripts\flannel-color.txt" -InputObject @(
  "n=$n"
  ("avg=#{0:X2}{1:X2}{2:X2}" -f $ar,$ag,$ab)
  "avg-rgb=$ar,$ag,$ab"
  ("max=#{0:X2}{1:X2}{2:X2}" -f $maxR,$maxG,$maxB)
) -Encoding ascii
