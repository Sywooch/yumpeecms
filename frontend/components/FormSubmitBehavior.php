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
namespace frontend\components;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use Yii;
use frontend\models\WebHookEmail;
use frontend\components\ContentBuilder;
use frontend\models\Twig;


class FormSubmitBehavior extends Behavior{
    public function events()
    {
        return [
            // after find event
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',            
        ];
    }
    public function afterSave(){
        //if we are instructed to not send the emails then ignore here
        if(Yii::$app->request->getBodyParam("ignore_send_email")=="true"):
            return "";
        endif;
        $form_id = $this->owner->form_id;
        $webhook_emails = WebHookEmail::find()->where(['form_id'=>$form_id])->all();
        $from_email = ContentBuilder::getSetting("smtp_sender_email");
        $from_name = ContentBuilder::getSetting("smtp_sender_name");
        $pattern = "/{yumpee_hook}(.*?){\/yumpee_hook}/";  //use this to capture form elements submitted
        $pattern_setting= "/{yumpee_setting}(.*?){\/yumpee_setting}/"; //use this to capture settings value in the settings page
        $pattern_twig= "/{yumpee_include}(.*?){\/yumpee_include}/";
        $pattern_record= "/{yumpee_record}(.*?){\/yumpee_record}/";
        
        foreach($webhook_emails as $webhook_email):
        if($webhook_email!=null):
            if($webhook_email->webhook_type=="N"):
            //we search and replace yumpee_hooks here with the filled out contents
            $message = preg_replace_callback($pattern,function ($matches) {
                            $request = Yii::$app->request;
                            $replacer="";
                            $replacer=$request->getBodyParam($matches[1]);
                            return $replacer;
                    },$webhook_email->message);
                    
            $message = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$message);
            
            $message = preg_replace_callback($pattern_twig,function ($matches) {
                            $loader = new Twig();
                            $twig = new \Twig_Environment($loader); 
                            $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');
                            $metadata['param'] = Yii::$app->request->csrfParam;
                            $metadata['token'] = Yii::$app->request->csrfToken;
                            $content= $twig->render(Twig::find()->where(['renderer'=>$matches[1]])->one()->filename,['app'=>Yii::$app,'metadata'=>$metadata]);
                            return $content;
                            //return $replacer;
                    },$message); 
                 
                    
            /*
             * If we are meant to include the form data on the email
             */
            if($webhook_email->include_data=="Y"):
                foreach(Yii::$app->request->bodyParams as $key):
                    $message.="<br>".$key.":".Yii::$app->request->getBodyParam($key);
                endforeach;
            endif;
            if(ContentBuilder::getSetting("outgoing_mail_processor")=="Local"):
                $to = $webhook_email->email;
		$headers = "From: " . [$from_email=>$from_name] . "\r\n";
		$headers .= "Reply-To: ". [$from_email=>$from_name] . "\r\n";				
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                mail($to, $webhook_email->subject, $message, $headers);
            else:
                Yii::$app->mailer->compose()
                ->setFrom([$from_email=>$from_name])
                ->setTo($webhook_email->email)
                ->setSubject($webhook_email->subject)
                ->setHtmlBody($message)
                ->send();
            endif;
            endif;
          
        
        
        if($webhook_email->webhook_type=="F"):
            $message = $array = preg_replace_callback($pattern,function ($matches) {
            $request = Yii::$app->request;
            $replacer="";
                $replacer=$request->getBodyParam($matches[1]);
                return $replacer;
            },$webhook_email->message);
            
            $message = preg_replace_callback($pattern_setting,function ($matches) {
                            $replaceme = ContentBuilder::getSetting($matches[1]);                            
                            return $replaceme;
                    },$message);
                    
            $message = preg_replace_callback($pattern_record,function ($matches) {
                            $field = $matches[1];
                            if($this->owner->$field!=null):
                                return $this->owner->$field;
                            endif;
                            return "";
            },$message);
            
            
                $to_email = $array = preg_replace_callback($pattern,function ($matches) {
                $request = Yii::$app->request;
                $replacer="";
                $replacer=$request->getBodyParam($matches[1]);
                return $replacer;
            },$webhook_email->email);
                
                
                
                if($webhook_email->include_data=="Y"):
                    foreach(Yii::$app->request->bodyParams as $key):
                        $message.="<br>".$key.":".Yii::$app->request->getBodyParam($key);
                    endforeach;
                endif;    
                if(ContentBuilder::getSetting("outgoing_mail_processor")=="Local"):
                    $to = $to_email;
                    $headers = "From: " . [$from_email=>$from_name] . "\r\n";
                    $headers .= "Reply-To: ". [$from_email=>$from_name] . "\r\n";				
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    mail($to, $webhook_email->subject, $message, $headers);
                else:
                    Yii::$app->mailer->compose()
                    ->setFrom([$from_email=>$from_name])
                    ->setTo($to_email)
                    ->setSubject($webhook_email->subject)
                    ->setHtmlBody($message)
                    ->send();
                endif;
                
            
        endif;
        //we attend to the webhook response email
        
        if($webhook_email->webhook_type=="R"):
                     
            $message = $array = preg_replace_callback($pattern,function ($matches) {
            $request = Yii::$app->request;
            $replacer="";
            
            $replacer=$request->getBodyParam($matches[1]);
                return $replacer;
            },$webhook_email->message);
            
            $message = preg_replace_callback($pattern_setting,function ($matches) {
                            $replaceme = ContentBuilder::getSetting($matches[1]);                            
                            return $replaceme;
                    },$message);
                    
            $message = preg_replace_callback($pattern_record,function ($matches) {
                            $field = $matches[1];
                            if($this->owner->$field!=null):
                                return $this->owner->$field;
                            endif;
                            return "";
            },$message);
            
            if($webhook_email->email=='0'):
                //this means get the email of the logged in user
                $to_email = Yii::$app->user->identity->email;   
                if($webhook_email->include_data=="Y"):
                    foreach(Yii::$app->request->bodyParams as $key):
                        $message.="<br>".$key.":".Yii::$app->request->getBodyParam($key);
                    endforeach;
                endif;    
                
                if(ContentBuilder::getSetting("outgoing_mail_processor")=="Local"):
                    $to = $to_email;
                    $headers = "From: " . [$from_email=>$from_name] . "\r\n";
                    $headers .= "Reply-To: ". [$from_email=>$from_name] . "\r\n";				
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    mail($to, $webhook_email->subject, $message, $headers);
                else:
                    Yii::$app->mailer->compose()
                    ->setFrom([$from_email=>$from_name])
                    ->setTo($to_email)
                    ->setSubject($webhook_email->subject)
                    ->setHtmlBody($message)
                    ->send();
                endif;
                
            endif;
        endif;
       endif;
       endforeach;
        
      
      
        
     //we attend to the external API calls
       
    }
}