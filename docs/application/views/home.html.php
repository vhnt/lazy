<div class="jumbotron">
    <div class="container">
        <div class="text-center">
            <h1><span class="text-success">Lazy Framework</span></h1>
            <h3><span class="text-danger">PHP Framework for Lazy Man</span></h3>
            <br>

            <div>
                <iframe src="http://ghbtns.com/github-btn.html?user=lazy-framework&repo=lazy&type=watch&count=true&size=large"
                        allowtransparency="true" frameborder="0" scrolling="0" width="150" height="30"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lazy-framework&repo=lazy&type=fork&count=true&size=large"
                        allowtransparency="true" frameborder="0" scrolling="0" width="150" height="30"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lazy-framework&repo=lazy&type=follow&count=true&size=large"
                        allowtransparency="true" frameborder="0" scrolling="0" width="150" height="30"></iframe>
            </div>
            <div>
                <a href="https://travis-ci.org/lazy-framework/lazy"><img src="https://travis-ci.org/lazy-framework/lazy.png"></a>
                <a href="https://coveralls.io/r/lazy-framework/lazy"><img src="https://coveralls.io/repos/lazy-framework/lazy/badge.png"></a>
                <a href="https://packagist.org/packages/lazy/lazy"><img src="https://poser.pugx.org/lazy/lazy/v/stable.png"></a>
                <a href="https://packagist.org/packages/lazy/lazy"><img src="https://poser.pugx.org/lazy/lazy/v/unstable.png"></a>
                <a href="https://packagist.org/packages/lazy/lazy"><img src="https://poser.pugx.org/lazy/lazy/downloads.png"></a>
            </div>
        </div>
    </div>
</div>

<div class="container">
<textarea class="code">
    # GET /
    $this->get('/', function() {
        return 'Hi Lazy Man, welcome to joining with us!';
    });

    # GET /posts
    $this->get('/posts', function() {
        return $this->render('posts/list', [
            'posts' => Post::all()->limit(10)
        ]);
    });

    # GET /posts/123
    $this->get('/posts/::id', function($id) {
        $post = Post::first($id);

        $post || $this->notFound();

        return $this->render('/posts/view', [
            'post' => $post
        ]);
    });

    # POST /posts
    $this->post('/posts', function() {
        $post = Post::create([
            'title' => $this->params->title,
            'content' => $this->params->content,
        ])->save();

        $this->redirectTo('/posts/' . $post->id);
    });

    # PUT /posts/123
    $this->put('/posts/::id', function($id) {
        $post = Post::first($id);
        $post || $this->notFound();

        $post->title    = $this->params->title;
        $post->content  = $this->params->content;

        $post->save();

        $this->redirectBack();
    });

    # DELETE /posts/123
    $this->delete('/posts/::id', function($id) {
        $post = Post::first($id);
        $post || $this->notFound();

        $post->delete();

        $this->redirectTo('/posts');
    });
</textarea>
</div>