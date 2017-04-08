<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
    
        <div class="container">
            <div class="content">
                <div class="title">Laravel 5</div>
            </div>
        <h1>{{ $fullname }}</h1>
        <p>
        @can('edit-post')
            <a href="#">Edit Post</a>
        @endcan
        </p>
        <p>
        @can('delete-post')
            <a href="#">Delete Post</a>
        @endcan
        </p>
        </div>
        
    </body>
</html>
