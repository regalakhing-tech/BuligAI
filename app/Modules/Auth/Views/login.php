<section class="narrow">
    <p class="eyebrow">YOUR COMMUNITY WORKSPACE</p>
    <h1>Welcome to<br>BuligAI.</h1>
    <p class="muted">Sign in to upload and manage your private files.</p>
    <form method="post" action="/login" class="login-form">
        <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" autocomplete="username" placeholder="you@example.com" required maxlength="254">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required maxlength="4096">
        <button type="submit">Sign in <span aria-hidden="true">↗</span></button>
    </form>
    <p class="small muted">Use your BuligAI community account. Need access? Contact the community administrator.</p>
</section>
