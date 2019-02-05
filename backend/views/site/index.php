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
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome to Yumpee CMS!</h1>

        <p class="lead">A scalable and customizable  Content Management System .</p>

        
    </div>

    <div class="body-content">

        <div class="container">
            <div class="col-lg-4">
                <h2>Pages</h2>

                <p>Build Web Pages quickly and publish them to the web. </p>

                <p><a class="btn btn-default" href="?r=pages/index">Manage Pages</a>
            </div>
            <div class="col-lg-4">
                <h2>Blogs</h2>

                <p>Create and manage Blog Articles. Categorize articles into different groups and design who can have access to these articles</p>

                <p><a class="btn btn-default" href="?r=articles/index">Manage Blogs</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Forms</h2>

                <p>Make your website very interractive with Form Management feature. Design and publish forms, and analyze the data collected from the forms</p>

                <p><a class="btn btn-default" href="?r=forms/index">Manage Forms</a></p>
            </div>
        </div>

    </div>
</div>
