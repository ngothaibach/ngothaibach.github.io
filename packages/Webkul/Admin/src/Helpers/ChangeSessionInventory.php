<?php
namespace Webkul\Admin\Helpers;
use Session;
class ChangeSessionInventory {
    public function ChangeInventory(){
        if(isset($_GET['inventory'])){
            if(auth()->guard('admin')->user()->role['id'] == 1){
                Session::put('inventory', $_GET['inventory']);      
            }
        }
        return redirect()->back();
    }
}
?>