<?
class UtilityController extends BackendController
{

  public function watchdog()
        {
                $utilityModel = BluApplication::getModel('utility');
                $utilityModel->watchdog();
        }

}
?>
