<link rel="stylesheet" href="{{ asset('general/css/prism.min.css') }}">
<style>
    .sidebar { padding: 2rem;  position: sticky; top: 72px; height: calc(100vh - 72px); overflow-y: auto; }
    .sidebar a { display: block; color: #333; padding: .5rem 1rem; text-decoration: none; font-weight: 500; border-left: 3px solid transparent; border-radius: 0 4px 4px 0; margin-bottom: .25rem; }
    .sidebar a.active, .sidebar a:hover { background: #e8f0fe; border-left-color: #0056b3; color: #0056b3; }

    .content-area { padding: 2rem; }
    .content-area section { scroll-margin-top: 120px; }
    .content-area h2 { margin-top: 2rem; margin-bottom: 1rem; color: #0056b3; font-weight: 600; }
    .content-area p, .content-area ul, .content-area ol { line-height: 1.6; color: #4a4a4a; }

    .code-block { position: relative; margin: 1.5rem 0; }
    .code-block pre { border-radius: 4px; overflow: auto; }
</style>