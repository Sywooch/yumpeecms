<?php
namespace frontend\controllers;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\components\ContentBuilder;
use frontend\models\SignupForm;
use frontend\models\Twig;
use frontend\models\Templates;
use backend\models\Pages;
use yii\helpers\Url;


class RegistrationController extends Controller{
    
    public function actionIndex(){
        
        $page =[];
        $form=[];
        $form['error_message']="";
        $model = new SignupForm();
        
        if (Yii::$app->request->post()) {
            $twig_set=ContentBuilder::getSetting("twig_template");
            $result = $model->registerNewUser();          
            
            if($result!=null && isset($result["id"])): 
                if(Yii::$app->request->post("mail-widget")!==null && Yii::$app->request->post("mail-widget")!="" && $twig_set=="Yes"):
                    $theme_id = ContentBuilder::getSetting("current_theme");
                    $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->post("mail-widget"),'renderer_type'=>'I'])->one();
                    $loader = new Twig();
                    $twig = new \Twig_Environment($loader);
                    $content= $twig->render($codebase['filename'], ['form'=>$form,'app'=>Yii::$app]);
                    $mail_content = $this->render('@frontend/views/layouts/html',['data'=>$content]);
                     Yii::$app->mailer->compose()
                    ->setFrom(ContentBuilder::getSetting("smtp_sender_email"))
                    ->setTo(Yii::$app->request->post("email"))
                    ->setSubject(Yii::$app->request->post("mail-subject")!==null ? Yii::$app->request->post("mail-subject") : "Thank you for registration")
                    ->setHtmlBody($mail_content)
                    ->send();
                endif;
                if(Yii::$app->request->post("auto-login")=="true"):
                    
                    $model = new \common\models\LoginForm();
                
                    $model->username=Yii::$app->request->post("username");
                    if(Yii::$app->request->post("login-type")=="email"):
                        $model->username=\backend\models\Users::find()->where(['email'=>Yii::$app->request->post("email")])->one()->username;
                    endif;
                    $model->password=Yii::$app->request->post("password");
                    $model->login(); 
                    
                endif;
                
                if(Yii::$app->request->post("return-type")=="json"):
                    $session = ["param"=>Yii::$app->request->csrfParam,"token"=>Yii::$app->request->csrfToken];
                //return \yii\helpers\Json::encode(["session"=>$session]);
                    
                    return Yii::$app->api->sendSuccessResponse($session);
                endif;
                if(Yii::$app->request->post("response-widget")!==null && Yii::$app->request->post("response-widget")!="" && $twig_set=="Yes"):
                    $theme_id = ContentBuilder::getSetting("current_theme");
                    $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>Yii::$app->request->post("response-widget"),'renderer_type'=>'I'])->one();
                    $loader = new Twig();
                    $twig = new \Twig_Environment($loader);
                    $content= $twig->render($codebase['filename'], ['form'=>$form,'app'=>Yii::$app]);
                    return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                endif;
                // we get the page we are meant to go to after registration
                $my_success_page = ContentBuilder::getSetting('registration_page');
                $my_home_page_object = Pages::find()->where(['id'=>$my_success_page])->one();
                
                if($my_home_page_object!=null):                        
                    Yii::$app->request->setScriptUrl($my_home_page_object->url);   
                    $this->redirect(array('/'.$my_home_page_object->url));
                    //return Yii::$app->runAction(ContentBuilder::getTemplateRouteByURL($my_home_page_object->url),['id'=>$my_home_page_object->url]);                
                else:
                    return $this->render('index');
                endif;
            else:
                //we flash the message we get
                if(Yii::$app->request->post("return-type")=="json"):
                    return \yii\helpers\Json::encode($result);
                endif;
                $form['error_message'] = $result["error"];
            endif;
        }
        
        
        
     
     $form['param'] = Yii::$app->request->csrfParam;
     $form['token'] = Yii::$app->request->csrfToken;
     $form['saveURL'] = \Yii::$app->getUrlManager()->createUrl(ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl()));
     $page_url =  ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
     $article = Pages::find()->where(['url'=>$page_url])->one();
     
     
     $renderer="registration/index";
     $view_file="account/registration";
      $template = Templates::find()->where(['id'=>$article->template])->one();
      if(!empty($template->parent_id)):
            $renderer = $template->renderer;
            $view_file="account/".$renderer;
      endif;
     if(ContentBuilder::getSetting("twig_template")=="Yes"):
                        //we handle the loading of twig template if it is turned on
                        $theme_id = ContentBuilder::getSetting("current_theme");
                        $codebase=Twig::find()->where(['theme_id'=>$theme_id,'renderer'=>$renderer,'renderer_type'=>'V'])->one();
                        if(($codebase!=null)&& ($codebase['code']<>"")):
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$form,'page'=>$article]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                        endif;
                    endif;
     return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/'.$view_file,['form'=>$form,'page'=>$article]);   
    }
    public function actionList(){
        $page =[];
        
     return $this->render('list',$page);   
    }
    
}
