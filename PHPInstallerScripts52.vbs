Const ForReading = 1
Const ForWriting = 2

sub configApache

    Dim objFSO
    Dim objFile

    strDirective = vbCrLf & vbCrLf & "#BEGIN PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL" & vbCrLf
    strApacheDir = Session.Property("APACHEDIR")
    strPHPPath = Replace(Session.TargetPath("INSTALLDIR"),"\","/")
    If ( right(strApacheDir,1) <> "/" ) then 
        strApacheDir = strApacheDir & "/"
    End If
    
    If ( Session.FeatureRequestState("apacheCGI") = 3 ) Then
		strDirective = strDirective & "ScriptAlias /php/ """ & strPHPPath & """" & vbCrLf
		strDirective = strDirective & "Action application/x-httpd-php """ & strPHPPath & "php-cgi.exe""" & vbCrLf
	End If
    
    If ( Session.FeatureRequestState("apache22") = 3 ) Then
		strDirective = strDirective & "PHPIniDir """ & strPHPPath & """" & vbCrLf
		strDirective = strDirective & "LoadModule php5_module """ & strPHPPath & "php5apache2_2.dll""" & vbCrLf
	End If
        
	If ( Session.FeatureRequestState("apache20") = 3 ) Then
		strDirective = strDirective & "PHPIniDir """ & strPHPPath & """" & vbCrLf
		strDirective = strDirective & "LoadModule php5_module """ & strPHPPath & "php5apache2.dll""" & vbCrLf
	End If
        
	If ( Session.FeatureRequestState("apache13") = 3 ) Then
		strDirective = strDirective & "PHPIniDir """ & strPHPPath & """" & vbCrLf
		strDirective = strDirective & "LoadModule php5_module """ & strPHPPath & "php5apache.dll""" & vbCrLf
	End If
    
    strDirective = strDirective &  "#END PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL" & vbCrLf
    
    Set objFSO = CreateObject("Scripting.FileSystemObject")
    strFileName = strApacheDir & "httpd.conf"
    If objFSO.FileExists(strFileName) Then
        Set objFile = objFSO.OpenTextFile( strFileName, ForReading)
    Else
        strFileName = strApacheDir & "conf\httpd.conf"
        If objFSO.FileExists(strFileName) Then
            Set objFile = objFSO.OpenTextFile( strFileName, ForReading)
        Else
            FatalError ("Error trying access httpd.conf file.")
            Exit Sub
        End If    
    End If        
    strText = objFile.ReadAll
    objFile.Close
    
    ' try and comment out old directives if they exist
    strText = Replace(strText,"ScriptAlias /php/","#ScriptAlias /php/")
    strText = Replace(strText,"Action application/x-httpd-php","#Action application/x-httpd-php")
    strText = Replace(strText,"PHPIniDir","#PHPIniDir")
    strText = Replace(strText,"LoadModule php5_module","#LoadModule php5_module")
    strText  = strText & strDirective
    
    ' backup old file
    strBackupFileName = strFileName & ".bak"
    objFSO.CopyFile strFileName, strBackupFileName 
    
    Set objFile = objFSO.OpenTextFile( strFileName, ForWriting)
    objFile.WriteLine strText
    objFile.Close
    
    strFileName = strApacheDir & "mime.types"
    If objFSO.FileExists(strFileName) Then
        Set objFile = objFSO.OpenTextFile( strFileName, ForReading)
    Else
        strFileName = strApacheDir & "conf\mime.types"
        If objFSO.FileExists(strFileName) Then
            Set objFile = objFSO.OpenTextFile( strFileName, ForReading)
        Else
            FatalError ("Error trying access mime.types file.")
            Exit Sub
        End If    
    End If
    
    strText = objFile.ReadAll
    objFile.Close
    
    If ( InStr(strText,"application/x-httpd-php") = 0 ) Then
        strText = strText & "application/x-httpd-php" & vbTab & "php" & vbCrLf
    End If
    
    If ( InStr(strText,"application/x-httpd-php-source") = 0 ) Then
        strText = strText & "application/x-httpd-php-source" & vbTab & "phps" & vbCrLf
    End If
    
    ' backup old file
    strBackupFileName = strFileName & ".bak"
    objFSO.CopyFile strFileName, strBackupFileName
    
    Set objFile = objFSO.OpenTextFile( strFileName, ForWriting)
    objFile.WriteLine strText
    objFile.Close
    
end sub

sub unconfigApache

    Dim objFSO
    Dim objFile

    strApacheDir = Session.Property("APACHEREGDIR")
    If ( right(strApacheDir,1) <> "\" ) then 
        strApacheDir = strApacheDir & "\"
    End If

    Set objFSO = CreateObject("Scripting.FileSystemObject")
    strFileName = strApacheDir & "httpd.conf"
    If objFSO.FileExists(strFileName) Then
        Set objFile = objFSO.OpenTextFile( strFileName, ForReading)
    Else
        strFileName = strApacheDir & "conf\httpd.conf"
        If objFSO.FileExists(strFileName) Then
            Set objFile = objFSO.OpenTextFile( strFileName, ForReading)
        Else
            FatalError ("Error trying access httpd.conf file.")
            Exit Sub
        End If    
    End If
    
    do while objFile.AtEndOfStream = false
        strText = objFile.ReadLine
        
        If ( strText = "#BEGIN PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL" ) Then
            strEndText = objFile.ReadLine
            do while strEndText <> "#END PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL"
                strEndText = objFile.ReadLine
            loop    
        Else
            strNewText = strNewText & vbCrLf & strText
        End If
    loop
    
    objFile.Close
    
    Set objFile = objFSO.OpenTextFile(strFileName, ForWriting)
    objFile.WriteLine strNewText
    objFile.Close

end sub

sub configIIS4

    Dim WebService
    Dim Paths
    Dim Nodes()
    Dim NumExtensions
    Dim PHPExecutable
    Dim NodeCount
    Dim FullPath
    Dim Args, Arg, ArgCount
    Dim I
    Dim J
    Dim K
    Dim MapNode, ScriptMaps, OutMaps(), Map, MapBits
 
    strPHPPath = Session.TargetPath("INSTALLDIR")
    If ( right(strPHPPath,1) <> "\" ) then 
        strPHPPath = strPHPPath & "\"
    End If
    If ( Session.FeatureRequestState("iis4CGI") = 3 ) Then
        PHPExecutable = strPHPPath & "php-cgi.exe"
    End If
    If ( Session.FeatureRequestState("iis4ISAPI") = 3 ) Then
        PHPExecutable = strPHPPath & "php5isapi.dll"
    End If
    If ( Session.FeatureRequestState("iis4FastCGI") = 3 ) Then
        Set objShell = CreateObject("WScript.Shell")
        Set colSystemEnvVars = objShell.Environment("System")
        PHPExecutable = colSystemEnvVars("WINDIR") & "\system32\inetsrv\fcgiext.dll"
    End If
    
    If ( GetWindowsVersion < 5.2 ) Then
        'use short path syntax here
        Set objFSO = CreateObject("Scripting.FileSystemObject")
        PHPExecutable = objFSO.GetFile(PHPExecutable).ShortPath
    Else
        'use quotes and long name syntax
        PHPExecutable = """" & PHPExecutable & """"
    End If
    
    'it could all go dreadfully wrong - so set error handler for graceful exits
    On Error Resume Next
 
    Set WebService = GetObject("IIS://LocalHost/W3SVC")
    If (Err.Number <> 0) Then
        FatalError ("Error trying access the local web service: GetObject Failed.")
        Exit Sub
    End If
    'I may be doing the wrong thing with inheritance here - it seems to work, however!
    Paths = WebService.GetDataPaths("scriptmaps", IIS_DATA_INHERIT)
    If Err.Number <> 0 Then Paths = WebService.GetDataPaths("scriptmaps", IIS_DATA_NO_INHERIT)
    If (Err.Number <> 0) Then
        FatalError ("Error trying to find the nodes containing scriptmaps :GetDataPaths Failed.")
        Exit Sub
    End If
    For Each FullPath In Paths
        Set MapNode = GetObject(FullPath)
        ReDim OutMaps(0)
        J = 0
        For Each Map In MapNode.ScriptMaps
            'split the extension from the scriptmap entry
            MapBits = Split(Map, ",")
            If MapBits(0) <> ".php" Then
                'if the extension doesn't match any of our php ones, preserve it
                ReDim Preserve OutMaps(J)
                OutMaps(J) = Map
                J = J + 1
            End If
        Next

        ReDim Preserve OutMaps(J + 1 - 1)

        'add our php extensions to OutMaps
        OutMaps(J) = ".php" & "," & PHPExecutable & ",1"
   
        'write the Outmap to the current node
        MapNode.Put "ScriptMaps", (OutMaps)
        'setinfo to make it so
        MapNode.SetInfo
    Next
    
End Sub

sub unconfigIIS4

    Dim WebService
    Dim Paths
    Dim Nodes()
    Dim NumExtensions
    Dim PHPExecutable
    Dim NodeCount
    Dim FullPath
    Dim Args, Arg, ArgCount
    Dim I
    Dim J
    Dim K
    Dim MapNode, ScriptMaps, OutMaps(), Map, MapBits
 
    'it could all go dreadfully wrong - so set error handler for graceful exits
    On Error Resume Next
 
    Set WebService = GetObject("IIS://LocalHost/W3SVC")
    If (Err.Number <> 0) Then
        FatalError ("Error trying access the local web service: GetObject Failed.")
        Exit Sub
    End If
    'I may be doing the wrong thing with inheritance here - it seems to work, however!
    Paths = WebService.GetDataPaths("scriptmaps", IIS_DATA_INHERIT)
    If Err.Number <> 0 Then Paths = WebService.GetDataPaths("scriptmaps", IIS_DATA_NO_INHERIT)
    If (Err.Number <> 0) Then
        FatalError ("Error trying to find the nodes containing scriptmaps :GetDataPaths Failed.")
        Exit Sub
    End If
    For Each FullPath In Paths
        Set MapNode = GetObject(FullPath)
        ReDim OutMaps(0)
        J = 0
        For Each Map In MapNode.ScriptMaps
            'split the extension from the scriptmap entry
            MapBits = Split(Map, ",")
            If MapBits(0) <> ".php" Then
                'if the extension doesn't match any of our php ones, preserve it
                ReDim Preserve OutMaps(J)
                OutMaps(J) = Map
                J = J + 1
            End If
        Next

        'write the Outmap to the current node
        MapNode.Put "ScriptMaps", (OutMaps)
        'setinfo to make it so
        MapNode.SetInfo
    Next

end sub

Sub FatalError(Message)
    MsgBox Message & " You will need to manually configure the web server.", vbExclamation, "Error"
End Sub

Function GetWindowsVersion
    Set objWMIService = GetObject("winmgmts:\\.\root\cimv2")
    Set colItems = objWMIService.ExecQuery("Select * from Win32_OperatingSystem",,48)
    For Each objItem in colItems
        ver=objItem.Version
    Next
    GetWindowsVersion = Left(ver,3)
End Function
