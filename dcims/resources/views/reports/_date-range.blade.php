<form method="GET" class="row g-2 align-items-end mb-4">
    <div class="col-auto">
        <x-input-label for="from" value="From" />
        <input type="date" id="from" name="from" class="form-control form-control-sm" value="{{ $from }}">
    </div>
    <div class="col-auto">
        <x-input-label for="to" value="To" />
        <input type="date" id="to" name="to" class="form-control form-control-sm" value="{{ $to }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-outline-primary">Apply</button>
    </div>
</form>
