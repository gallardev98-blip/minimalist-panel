<div class="panel-table-wrap" aria-hidden="true">
    <div class="overflow-x-auto p-4">
        <div class="space-y-3">
            @for ($i = 0; $i < 5; $i++)
                <div class="flex gap-4">
                    <div class="panel-skeleton h-4 w-8 rounded"></div>
                    <div class="panel-skeleton h-4 flex-1 rounded"></div>
                    <div class="panel-skeleton h-4 w-24 rounded"></div>
                    <div class="panel-skeleton h-4 w-16 rounded"></div>
                </div>
            @endfor
        </div>
    </div>
</div>
