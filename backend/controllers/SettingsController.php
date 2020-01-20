<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\Helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Themes;
use backend\models\Media;
use backend\models\Pages;
use backend\models\Roles;
use backend\models\Templates;
use backend\models\CustomSettings;
use backend\models\Forms;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;

class SettingsController extends Controller{
 public function behaviors()
{
    if(Settings::find()->where(['setting_name'=>'use_custom_backend_menus'])->one()->setting_value=="on" && !Yii::$app->user->isGuest):
    $can_access=1;
    $route = "/".Yii::$app->request->get("r");
    //check to see if route exists in our system
    $menu_rec = BackEndMenus::find()->where(['url'=>$route])->one();
    if($menu_rec!=null):
        //we now check that the current role has rights to use it
        $role_access = BackEndMenuRole::find()->where(['menu_id'=>$menu_rec->id,'role_id'=>Yii::$app->user->identity->role_id])->one();
        if(!$role_access):
            //let's take a step further if there is a custom module
            $can_access=0;            
        endif;
    endif;
    if($can_access < 1):
        echo "You do not have permission to view this page";
        exit;
    endif;
    endif;
    
    return [
        'access' => [
            'class' => \yii\filters\AccessControl::className(),
            'only' => ['create', 'update'],
            'rules' => [
                // deny all POST requests
                [
                    'allow' => false,
                    'verbs' => ['POST']
                ],
                // allow authenticated users
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
                // everything else is denied
            ],
        ],
    ];
}     
    public function actionIndex(){
      $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Settings::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Settings::find()->where(['id' => "0"])->one();
        endif;
        
      $page['custom_id'] = Yii::$app->request->get('custom_id',null);
      if($page['custom_id']!=null):
            $page['custom_rs'] = CustomSettings::find()->where(['id' => $page['custom_id']])->one();
          else:
            $page['custom_rs'] = CustomSettings::find()->where(['id' => "0"])->one();
      endif; 
        $custom_forms = Forms::find()->select('id')->where(['show_in_menu'=>'Y'])->column();
        $themes_list = Themes::find()->all();
        $page['themes'] = ArrayHelper::map($themes_list, 'id', 'name');
        
        $default_page = array("");
        $logout_template = Templates::find()->where(['route'=>'accounts/logout'])->one();
        $pages_list_home = Pages::find()->where('template !="'.$logout_template["id"].'"')->orderBy('title')->all();
        $back_pages_list = Pages::find()->where(['IN','form_id',$custom_forms])->all();
        $pages_list = Pages::find()->orderBy('title')->all();
        $page['pages'] = ArrayHelper::map($pages_list_home, 'id', 'menu_title');
        $page['error_pages'] = ArrayHelper::map($pages_list, 'id', 'menu_title');
        $page['maintenance_pages'] = ArrayHelper::map($pages_list_home, 'id', 'menu_title');
        $page['backend_home_pages'] = ArrayHelper::map($back_pages_list, 'id', 'menu_title');
        
        $page['time_zone'] = $this::TimeZone();
        
        array_push($page['pages'],"Default Home Page");
        array_push($page['error_pages'],"Default Error Page");
        array_push($page['maintenance_pages'],"Select Default Maintenance Page");
        array_push($page['backend_home_pages'],"Select Default Backend Home Page");
        $role_list = Roles::find()->orderBy('name')->all();
        $page['registration_roles'] = ArrayHelper::map($role_list, 'id', 'name');
        array_push($page['registration_roles'],"Select Default Registration Role");
        
        $page['records'] = Settings::find()->all();
        $display_image_obj = Settings::find()->where(['setting_name'=>'website_logo'])->one();
        $page['display_image_path'] = Media::find()->where(['id'=>$display_image_obj['setting_value']])->one();
        $fav_icon_obj = Settings::find()->where(['setting_name'=>'fav_icon'])->one();
        $page['fav_icon_path'] = Media::find()->where(['id'=>$fav_icon_obj['setting_value']])->one();
        $page['custom_records'] =CustomSettings::find()->where(['theme_id'=>NULL])->all();
        return $this->render('index',$page);  
    }
    
    public function actionSave(){
        //we carter for thos who may not be in the settings table here
        foreach($_POST as $key => $value):
            if($key=="id" || $key=="processor"):
                continue;
            endif;
            $settings_obj = Settings::find()->where(['setting_name'=>$key])->one();
            if($settings_obj==null):
                $model = new Settings();
                $model->setAttribute('setting_name',$key);
                $model->setAttribute('setting_value',$value);
                $model->save();
            endif;
        endforeach;
        
        $settings_obj = Settings::find()->all();
        foreach($settings_obj as $setting_val):
            $setting_val->setting_value = Yii::$app->request->post($setting_val->setting_name);
            $setting_val->update();
        endforeach;
        return "Settings successfully saved";
    }
    
    public function actionCustomSave(){
        $model = CustomSettings::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                
                $model->setting_name="custom_".Yii::$app->request->post("setting_name");
                $model->setting_value= Yii::$app->request->post("setting_value");
                $model->description = Yii::$app->request->post("description");
                $model->save();
                return "Custom Setting successfully updated";
            else:
                $model =  new CustomSettings();
                $model->id = md5(date("YmdHis").rand(1000,10000));
                $model->setting_name="custom_".Yii::$app->request->post("setting_name");
                $model->setting_value= Yii::$app->request->post("setting_value");
                $model->description = Yii::$app->request->post("description");
                $model->save();
                return "New Custom Setting created";
            endif;
    }
    public function actionDeleteCustom(){
        $id = str_replace("}","",Yii::$app->request->get("id"));    
        $a = CustomSettings::findOne($id);
        $a->delete();
        echo "Record successfully deleted";
    }
    
    public static function TimeZone($selected=''){
        
    $OptionsArray = timezone_identifiers_list();
        $select= '<select name="time_zone" id="time_zone" class="form-control">';
        while (list ($key, $row) = each ($OptionsArray) ){
            $select .='<option value="'.$row.'"';
            $select .= ($key == $selected ? ' selected' : '');
            $select .= '>'.$row.'</option>';
        }  // endwhile;
        $select.='</select>';
return $select;

        
    }
}

