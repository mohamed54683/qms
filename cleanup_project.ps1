# Clean up unnecessary files from the project
$projectPath = "C:\xampp\htdocs\gs-qms-system--main"

Write-Host "Starting cleanup process..." -ForegroundColor Yellow

# Count files before cleanup
$beforeCount = (Get-ChildItem -Path $projectPath -Recurse -File).Count
Write-Host "Total files before cleanup: $beforeCount" -ForegroundColor Cyan

# 1. Delete all markdown documentation files except README.md
Write-Host "`nDeleting documentation files..." -ForegroundColor Green
Get-ChildItem -Path $projectPath -Filter "*.md" -File | Where-Object { $_.Name -ne "README.md" } | ForEach-Object {
    Write-Host "  Removing: $($_.Name)" -ForegroundColor Gray
    Remove-Item $_.FullName -Force
}

# 2. Delete all test/check/debug PHP files
Write-Host "`nDeleting test/debug PHP files..." -ForegroundColor Green
$patterns = @("*test*", "*check*", "*debug*", "*reset*", "*demo*", "*setup*", "*final*", "*fix*", "*delete*", "*export*", "*deployment*", "*simple*", "*verify*")
foreach ($pattern in $patterns) {
    Get-ChildItem -Path $projectPath -Filter "$pattern.php" -File | ForEach-Object {
        Write-Host "  Removing: $($_.Name)" -ForegroundColor Gray
        Remove-Item $_.FullName -Force
    }
}

# 3. Delete batch files
Write-Host "`nDeleting batch files..." -ForegroundColor Green
Get-ChildItem -Path $projectPath -Filter "*.bat" -File | ForEach-Object {
    Write-Host "  Removing: $($_.Name)" -ForegroundColor Gray
    Remove-Item $_.FullName -Force
}

# 4. Delete old version directories
Write-Host "`nDeleting old version directories..." -ForegroundColor Green
Get-ChildItem -Path $projectPath -Directory | Where-Object { $_.Name -like "*READY*" -or $_.Name -like "*deployment*" -or $_.Name -like "*backup*" -or $_.Name -like "*old*" } | ForEach-Object {
    Write-Host "  Removing: $($_.Name)" -ForegroundColor Gray
    Remove-Item $_.FullName -Recurse -Force
}

# 5. Delete other unnecessary files
Write-Host "`nDeleting other unnecessary files..." -ForegroundColor Green
$filesToDelete = @(
    "sultandb.html",
    "deploy.sh",
    "gsqms_deployment.sql",
    "route-list.txt",
    "Show.vue.backup"
)
foreach ($file in $filesToDelete) {
    $filePath = Join-Path $projectPath $file
    if (Test-Path $filePath) {
        Write-Host "  Removing: $file" -ForegroundColor Gray
        Remove-Item $filePath -Force
    }
}

# Count files after cleanup
$afterCount = (Get-ChildItem -Path $projectPath -Recurse -File).Count
$deletedCount = $beforeCount - $afterCount

Write-Host "`n=== Cleanup Complete ===" -ForegroundColor Green
Write-Host "Files before: $beforeCount" -ForegroundColor Cyan
Write-Host "Files after: $afterCount" -ForegroundColor Cyan
Write-Host "Files deleted: $deletedCount" -ForegroundColor Yellow
Write-Host "`nProject is now clean and ready for hosting!" -ForegroundColor Green
