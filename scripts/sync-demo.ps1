param(
    [string] $DemoPath = "..\cicd_startseite_demo",
    [switch] $Push
)

$ErrorActionPreference = "Stop"

$sourceRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$targetRoot = (Resolve-Path (Join-Path $sourceRoot $DemoPath)).Path

if (!(Test-Path -LiteralPath (Join-Path $targetRoot ".git"))) {
    throw "Ziel ist kein Git-Repository oder .git fehlt: $targetRoot"
}

if ((Split-Path -Leaf $targetRoot) -ne "cicd_startseite_demo") {
    throw "Unerwartetes Sync-Ziel: $targetRoot"
}

$excludeDirs = @(
    ".git",
    "assets\icons"
)

$excludeFiles = @(
    ".env"
)

$robocopyArgs = @(
    $sourceRoot,
    $targetRoot,
    "/MIR",
    "/XD"
) + $excludeDirs + @(
    "/XF"
) + $excludeFiles + @(
    "/R:2",
    "/W:2",
    "/NFL",
    "/NDL"
)

& robocopy @robocopyArgs
$exitCode = $LASTEXITCODE

if ($exitCode -gt 7) {
    throw "robocopy fehlgeschlagen mit Exitcode $exitCode"
}

Write-Host "Demo-Repository wurde vollständig synchronisiert: $targetRoot"
Write-Host "Ausgeschlossen wurden: .git, .env und assets/icons"

$targetIconsPath = Join-Path $targetRoot "assets\icons"
if (Test-Path -LiteralPath $targetIconsPath) {
    $resolvedTargetIconsPath = (Resolve-Path -LiteralPath $targetIconsPath).Path
    if (!$resolvedTargetIconsPath.StartsWith($targetRoot)) {
        throw "Unerwarteter Icon-Zielpfad: $resolvedTargetIconsPath"
    }

    Remove-Item -LiteralPath $resolvedTargetIconsPath -Recurse -Force
    Write-Host "Alter assets/icons-Ordner im Demo wurde entfernt."
}

git -C $targetRoot status --short

if ($Push) {
    git -C $targetRoot add .
    git -C $targetRoot commit -m "Sync demo from source project"
    git -C $targetRoot push origin main
}
