<?php
function checkPermission($per){
    $allowedPermissions = auth()->guard('admin')->user()->role->permissions;
    if(empty($allowedPermissions)) {
        return True;
    }else{
        if(in_array($per,$allowedPermissions)){
            return True;
        }else{
            return False;
        }
    }
}