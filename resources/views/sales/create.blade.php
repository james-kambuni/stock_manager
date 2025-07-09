@extends('layouts.users')
@section('content')
<div class="container mt-4">
    <h2>Create Sale</h2>
    <form method="POST" action="{{ route('sales.store') }}">
        @csrf

        <table id="items" class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="width: 100px;">Quantity</th>
                    <th style="width: 120px;">Unit Price</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
                <tr data-index="0">
                    <td>
                        <select name="items[0][product_id]" class="form-select">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[0][quantity]" class="form-control" value="1" min="1"></td>
                    <td><input type="text" name="items[0][unit_price]" class="form-control" placeholder="0.00"></td>
                    <td><button type="button" class="btn btn-danger removeRow" disabled>–</button></td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="addItem" class="btn btn-secondary">Add Product</button>
        <button type="submit" class="btn btn-primary">Submit Sale</button>
    </form>
</div>

<script>
// JavaScript to add/remove item rows dynamically
document.getElementById('addItem').addEventListener('click', function() {
    let table = document.getElementById('items').getElementsByTagName('tbody')[0];
    let newIndex = table.rows.length;
    // Clone the first row and update input names
    let newRow = table.rows[0].cloneNode(true);
    newRow.dataset.index = newIndex;
    newRow.querySelectorAll('input, select, button.removeRow').forEach(function(el) {
        let name = el.getAttribute('name');
        if (name) {
            el.setAttribute('name', name.replace('0', newIndex));
        }
        if (el.tagName === 'INPUT') {
            el.value = '';
        }
    });
    // Enable remove button on cloned row
    newRow.querySelector('button.removeRow').disabled = false;
    newRow.querySelector('button.removeRow').addEventListener('click', function(){
        this.closest('tr').remove();
    });
    table.appendChild(newRow);
});
</script>
@endsection
