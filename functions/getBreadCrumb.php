<?php
    // Basic BreadCrumb
    // Type-hinted for clarity (string $title, string ...$items).
    // HTML escaped to stay safe.
    // One-liner return for readability.

    function getBreadCrumb(string $title, $items = null): void {
        if (is_string($items)) {
            $items = array_map('trim', explode(',', $items)); // split by comma
        }

    echo ("
    <div class='card'>
        <div class='card-body py-3 px-3'>
            <nav class='d-flex justify-content-between align-items-center' aria-label='breadcrumb'>
                <div class='fw-bold text-secondary'>
                    " . htmlspecialchars($title) . "
                </div>
                <ol class='breadcrumb mb-0'>
    ");

    if (!empty($items)) {
        foreach ($items as $i => $item) {
            $isLast = $i === array_key_last($items);
            $class  = $isLast ? "breadcrumb-item active" : "breadcrumb-item";
            echo "<li class='{$class}'>" . htmlspecialchars($item) . "</li>";
        }
    }

    echo ("
                </ol>
            </nav>
        </div>
    </div>
    ");
}

