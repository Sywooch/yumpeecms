<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\Helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Settings;
use backend\models\Themes;
use backend\models\Media;
use backend\models\Pages;
use backend\models\Roles;
use backend\models\Templates;
use backend\models\CustomSettings;

class SettingsController extends Controller{
    
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
        
        $themes_list = Themes::find()->all();
        $page['themes'] = ArrayHelper::map($themes_list, 'id', 'name');
        
        $default_page = array("");
        $logout_template = Templates::find()->where(['route'=>'accounts/logout'])->one();
        $pages_list_home = Pages::find()->where('template !="'.$logout_template["id"].'"')->orderBy('title')->all();
        
        $pages_list = Pages::find()->orderBy('title')->all();
        $page['pages'] = ArrayHelper::map($pages_list_home, 'id', 'menu_title');
        $page['error_pages'] = ArrayHelper::map($pages_list, 'id', 'menu_title');
        $page['maintenance_pages'] = ArrayHelper::map($pages_list_home, 'id', 'menu_title');
        
        array_push($page['pages'],"Default Home Page");
        array_push($page['error_pages'],"Default Error Page");
        array_push($page['maintenance_pages'],"Select Default Maintenance Page");
        $role_list = Roles::find()->orderBy('name')->all();
        $page['registration_roles'] = ArrayHelper::map($role_list, 'id', 'name');
        array_push($page['registration_roles'],"Select Default Registration Role");
        
        $page['records'] = Settings::find()->all();
        $display_image_obj = Settings::find()->where(['setting_name'=>'website_logo'])->one();
        $page['display_image_path'] = Media::find()->where(['id'=>$display_image_obj['setting_value']])->one();
        $fav_icon_obj = Settings::find()->where(['setting_name'=>'fav_icon'])->one();
        $page['fav_icon_path'] = Media::find()->where(['id'=>$fav_icon_obj['setting_value']])->one();
        $page['custom_records'] =CustomSettings::find()->all();
        return $this->render('index',$page);  
    }
    public function actionSave(){
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
}

