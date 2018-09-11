<div class="form-group">
    <label for="inputEmail">Select the Commodity</label>
    <select name="category_name" id="category_name" required class="form-control active input-medium">
        <option value="">Please select the type of the Commodity file you want to Upload</option>
        <option value="sugar">Sugar</option>
        <option value="platinum">Platinum</option>
        <option value="coffee">Coffee</option>
        <option value="oil">Oil</option>
        <option value="cocoa">Cocoa</option>
        <option value="silver">Silver</option>
        <option value="live_cattle">Live Cattle</option>
        <option value="gold">Gold</option>
        <option value="corn">Corn</option>
        <option value="wheat">Wheat</option>
    </select>
</div>
<div class="form-group">
    <label for="inputEmail">Difference</label>
    <input type="number" step="any" class="form-control" name="difference" placeholder="difference" required>
</div>
<div class="form-group">
    <label for="inputEmail">1st day value</label>
    <input type="number" step="any" class="form-control" name="open[]" placeholder="Open Value" required>
    <input type="number" step="any" class="form-control" name="high[]" placeholder="High Value" required>
    <input type="number" step="any" class="form-control" name="low[]" placeholder="Low Value" required>
    <input type="number" step="any" class="form-control" name="close[]" placeholder="Close Value" required>
</div>
<div class="form-group">
    <label for="inputEmail">2nd day value</label>
    <input type="number" step="any" class="form-control" name="open[]" placeholder="Open Value" required>
    <input type="number" step="any" class="form-control" name="high[]" placeholder="High Value" required>
    <input type="number" step="any" class="form-control" name="low[]" placeholder="Low Value" required>
    <input type="number" step="any" class="form-control" name="close[]" placeholder="Close Value" required>
</div>
<div class="form-group">
    <label for="inputEmail">3rd day value</label>
    <input type="number" step="any" class="form-control" name="open[]" placeholder="Open Value" required>
    <input type="number" step="any" class="form-control" name="high[]" placeholder="High Value" required>
    <input type="number" step="any" class="form-control" name="low[]" placeholder="Low Value" required>
    <input type="number" step="any" class="form-control" name="close[]" placeholder="Close Value" required>
</div>
<div class="form-group">
    <label for="inputEmail">4th day value</label>
    <input type="number" step="any" class="form-control" name="open[]" placeholder="Open Value" required>
    <input type="number" step="any" class="form-control" name="high[]" placeholder="High Value" required>
    <input type="number" step="any" class="form-control" name="low[]" placeholder="Low Value" required>
    <input type="number" step="any" class="form-control" name="close[]" placeholder="Close Value" required>
</div>
<div id="extra_div">

</div>
<input type="hidden" id="counter" value="4">