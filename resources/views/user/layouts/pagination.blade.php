@if ($dataSet->lastPage() > 1)

<div class="d-flex justify-content-between align-items-center p-2">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mb-0">
            {{-- Previous Page Link --}}
            <li class="page-item {{ $dataSet->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $dataSet->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            {{-- Pagination Links --}}
            @for ($i = 1; $i <= $dataSet->lastPage(); $i++)
                <li class="page-item {{ $i == $dataSet->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $dataSet->url($i) }}">{{ $i }}</a>
                </li>
                @endfor

                {{-- Next Page Link --}}
                <li class="page-item {{ $dataSet->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $dataSet->nextPageUrl() }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
        </ul>
    </nav>
    <p class="m-0 pe-2">
        Showing {{ $dataSet->firstItem() }} - {{ $dataSet->lastItem() }} of {{ $dataSet->total() }} results
    </p>
</div>
@endif