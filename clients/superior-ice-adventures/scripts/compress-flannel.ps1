Add-Type -AssemblyName System.Drawing
$src = Join-Path $PSScriptRoot "..\assets\img\textures\flannel-red-black.png"
$src = [System.IO.Path]::GetFullPath($src)
$img = [System.Drawing.Image]::FromFile($src)
$bmp = New-Object System.Drawing.Bitmap 256, 256
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$g.DrawImage($img, 0, 0, 256, 256)
$g.Dispose()
$img.Dispose()
$tmp = $src + ".tmp.png"
$bmp.Save($tmp, [System.Drawing.Imaging.ImageFormat]::Png)
$bmp.Dispose()
Move-Item -Force $tmp $src
Write-Output ((Get-Item $src).Length)
