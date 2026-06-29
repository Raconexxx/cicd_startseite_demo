Option Explicit

Dim shell, fso, scriptDir, port, url, startServerCommand, openDashboardCommand

Set shell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

scriptDir = fso.GetParentFolderName(WScript.ScriptFullName)
port = 8000
url = "http://127.0.0.1:" & port & "/internal_dashboard.html"

' Startet den lokalen Webserver versteckt aus dem Projektordner.
startServerCommand = "cmd /c cd /d """ & scriptDir & """ && python -m http.server " & port
shell.Run startServerCommand, 0, False

' Kurze Wartezeit, damit der Server sauber hochfahren kann.
WScript.Sleep 2000

' Offnet das Dashboard im Standardbrowser.
openDashboardCommand = "cmd /c start """" """ & url & """"
shell.Run openDashboardCommand, 0, False
