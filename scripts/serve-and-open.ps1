Param(
    [int]$Port = 8000,
    [string]$HostName = '127.0.0.1'
)

# Start Laravel dev server in a new background process
Write-Host ("Starting artisan serve on {0}:{1}..." -f $HostName, $Port)
Start-Process -FilePath "php" -ArgumentList "artisan","serve","--host=$HostName","--port=$Port"

# Wait a bit for server to boot
Start-Sleep -Seconds 1

$url = ("http://{0}:{1}" -f $HostName, $Port)
Write-Host ("Opening browser to {0}" -f $url)
Start-Process -FilePath $url

Write-Host "Done. Server should be running; press Ctrl+C in the server window to stop it."
