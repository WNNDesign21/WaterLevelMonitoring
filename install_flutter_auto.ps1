Write-Host "=========================================="
Write-Host "   Flutter Auto-Installer by AI Agent     "
Write-Host "=========================================="
Write-Host "1. Downloading Flutter SDK from fast Asian Mirror (Bypassing Timeouts)..."

$source = "https://storage.flutter-io.cn/flutter_infra_release/releases/stable/windows/flutter_windows_3.24.3-stable.zip"
$dest = "C:\src\flutter_bypass.zip"

Invoke-WebRequest -Uri $source -OutFile $dest
Write-Host "`nDownload Finished Successfully!"

Write-Host "2. Extracting Flutter. This might take 1-2 minutes..."
Expand-Archive -Path $dest -DestinationPath C:\src\ -Force

Write-Host "3. Adding Flutter to System PATH..."
$currentPath = [Environment]::GetEnvironmentVariable("Path", "User")
if ($currentPath -notmatch "C:\\src\\flutter\\bin") {
    [Environment]::SetEnvironmentVariable("Path", $currentPath + ";C:\src\flutter\bin", "User")
}

Write-Host "=========================================="
Write-Host "   INSTALLATION COMPLETE!                 "
Write-Host "=========================================="
Write-Host "Next Steps:"
Write-Host "Simply close ALL current terminal windows, open a new one, go to the mobile app folder, and run 'flutter run'."
