@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link {{ $paginator->onFirstPage() ? 'disabled':'' }}" href="{{ $paginator->onFirstPage() ? "javascript: void(0);":$paginator->previousPageUrl() }}" aria-label="Previous">
                    Previous
                </a>
            </li>
{{--            <li class="page-item"><a class="page-link" href="#">1</a></li>--}}
{{--            <li class="page-item"><a class="page-link" href="#">2</a></li>--}}
{{--            <li class="page-item"><a class="page-link" href="#">3</a></li>--}}
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() ? $paginator->nextPageUrl():"javascript: void(0);" }}" aria-label="Next">
                    Next
                </a>
            </li>
        </ul>
    </nav>
@endif
