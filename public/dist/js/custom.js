if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('__service-worker.js')
            .then(reg => console.log('service worker registered'))
            .catch(err => console.log('service worker not registered - there is an error.', err));
    }
function _(el) {
    return document.getElementById(el);
}

function confirmlogsheet() {
    try {
        var data = $("#logsheetform").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/logsheet/confirmlogsheet',
            data: data,
            success: function (data) {
                _("detail").innerHTML = data;
                _("conf").click();
            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function saveMISEmployee() {
    try {
        data = '';
        var aaa = document.getElementById('emp_name').value;
        $.ajax({
            type: 'GET',
            url: '/admin/mis/saveemployee/' + aaa,
            data: data,
            success: function (data) {
                if (data == 'exist') {
                    alert('Employee alredy exist');
                } else {
                    addSelect('emp', data);
                    document.getElementById('closebtn2').click();
                }
            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function saveMISLocation() {
    try {
        data = '';
        var location = document.getElementById('mislocation').value;
        var km = document.getElementById('location_km').value;
        $.ajax({
            type: 'GET',
            url: '/admin/mis/savemislocation/' + location + '/' + km,
            data: data,
            success: function (data) {
                if (data == 'exist') {
                    alert('Location alredy exist');
                } else {
                    addSelect('location', data);
                    document.getElementById('closebtn3').click();
                }
            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function addSelect(id, val) {
    var newStateVal = val;
    // Set the value, creating a new option if necessary
    if ($("#" + id).find("option[value=" + newStateVal + "]").length) {
        $("#" + id).val(newStateVal).trigger("change");
    } else {
        // Create the DOM option that is pre-selected by default
        var newState = new Option(newStateVal, newStateVal, true, true);
        // Append it to the select
        $("#" + id).append(newState).trigger('change');
    }
}

function confirmMis() {
    try {
        var data = $("#logsheetform").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/mis/confirmmis',
            data: data,
            success: function (data) {
                _("detail").innerHTML = data;
                _("conf").click();
            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function confirmCompanyMis() {
    try {
        _("conf").click();
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function saveLogsheet() {
    try {
        var data = $("#logsheetform").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/logsheet/savelogsheet',
            data: data,
            success: function (data) {
                _("loaded_n_total").innerHTML = data;
                _("startkm").value = '';
                _("endkm").value = '';
                _("toll").value = '';
                _("remark").value = '';
                _("closebtn").click();
            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function saveMis() {
    try {
        var data = $("#logsheetform").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/mis/savemis',
            data: data,
            success: function (data) {
                _("loaded_n_total").innerHTML = data;
                _("logsheet_no").value = '';
                _("toll").value = '';
                _("remark").value = '';
                _("closebtn").click();
            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function saveCompanyMis() {
    try {
        var data = $("#logsheetform").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/mis/savecompanymis',
            data: data,
            success: function (data) {
                _("loaded_n_total").innerHTML = data;
				alert(data);
				if(document.getElementById('id').value>0)
				{
					window.location.href = "/admin/mis/listmiscompany";

				}

            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function deleteEntry(link, cur) {
    try {
        var data = '';
        $.ajax({
            type: 'GET',
            url: '/admin/logsheet/deletebill/' + link,
            data: data,
            success: function (data) {
                $(cur).closest('tr').remove();
            }
        });
    } catch (o) {
        alert(o.message);
    }
    return false;
}

function logsheetDiv() {
    _("list").style.display = 'none';
    _("insert").style.display = 'block';
}

function typechange(val) {
    if (val == 2) {
        _('location').style.display = 'block';
        _('normal').style.display = 'none';
    } else {

        _('location').style.display = 'none';
        _('normal').style.display = 'block';
    }
}

function pickupdrop(val) {
    if (val == 'DROP') {
        document.getElementById('from_loc').value = 'Company';
        document.getElementById('to_loc').value = '';
    } else {

        document.getElementById('to_loc').value = 'Company';
        document.getElementById('from_loc').value = '';
    }
}

function calculateFuel() {
    qty = _("qty").value;
    rate = _("rate").value;
    amt = Number(qty * rate);
    _("amt").value = amt.toFixed(2);
}

function fuelIntrest() {
    employee_id = _("employee_id").value;
    if (employee_id > 0) {
        _("intrest").value = '5';
    } else {
        _("intrest").value = '';
    }

}

function calculateLogsheet() {
    total_amount = 0;
    $('input[name="int[]"]').each(function () {
        var id = $(this).val();
        var deduct = _("is_deduct" + id).value;
        qty = _("qty" + id).value;
        rate = _("rate" + id).value;
        amt = '';
        if (qty > 0 && rate > 0) {
            amt = Number(qty * rate);
            
            _("amt" + id).value = amt.toFixed(2);
        }
		amt=Number(_("amt" + id).value);
		if (deduct == 0) {
                total_amount = Number(total_amount + amt);
            } else {
                total_amount = Number(total_amount - amt);
            }

    });
    _("base_total").value = total_amount.toFixed(2);

    cgst = getamt('cgst');
    sgst = getamt('sgst');
    igst = getamt('igst');
    cgst_amt = roundA(total_amount * cgst / 100);
    sgst_amt = roundA(total_amount * sgst / 100);
    igst_amt = roundA(total_amount * igst / 100);
    _("cgst_amt").value = cgst_amt;
    _("sgst_amt").value = sgst_amt;
    _("igst_amt").value = igst_amt;

    total_gst = Number(cgst_amt) + Number(sgst_amt) + Number(igst_amt);
    _("total_gst").value = roundA(total_gst);
    _("grand_total").value = roundA(Number(total_amount) + Number(total_gst));


}

function invexpense() {
    var amount = 0;
    $('input[name="rcheck[]"]').each(function () {
        var check = $(this).prop("checked");
        if (check == true) {
            id = $(this).val();
            amt = Number(_("req_" + id).value);
            amount = amount + amt;
        }
    });
    _("total_expense").innerHTML=roundA(amount);
    _("expense_amount").value=roundA(amount);
}

function calculateIdeaLogsheet() {
    total_amount = 0;
    $('input[name="int[]"]').each(function () {
        var id = $(this).val();
        var deduct = _("is_deduct" + id).value;
        amt = getamt("amt" + id);
        if (amt > 0) {
            if (deduct == 0) {
                total_amount = Number(total_amount + amt);
            } else {
                total_amount = Number(total_amount - amt);
            }
        }

    });
    toll = getamt('amt8');
    total_amount = Number(total_amount - toll);
    _("base_total").value = total_amount.toFixed(2);

    cgst = getamt('cgst');
    sgst = getamt('sgst');
    igst = getamt('igst');
    cgst_amt = roundA(total_amount * cgst / 100);
    sgst_amt = roundA(total_amount * sgst / 100);
    igst_amt = roundA(total_amount * igst / 100);
    _("cgst_amt").value = cgst_amt;
    _("sgst_amt").value = sgst_amt;
    _("igst_amt").value = igst_amt;

    total_gst = Number(cgst_amt) + Number(sgst_amt) + Number(igst_amt);
    _("total_gst").value = roundA(total_gst);
    _("grand_total").value = roundA(Number(total_amount) + Number(total_gst) + Number(toll));


}

function getamt(amt) {
    try {
        amt = _(amt).value;
        if (amt > 0) {
            return Number(amt);
        } else {
            return 0;
        }
    } catch (o) {
        return 0;
    }
}

function addPassengers(total) {
    var i;
    var text = '';
    for (i = 0; i < total; i++) {
        text += '<div class="form-group"><label class="control-label col-md-4">Passenger ' + Number(i + 1) + ' Name<span class="required"> </span></label><div class="col-md-4"><input type="text" name="passengers_name[]" placeholder="Name" value="" class="form-control"></div><div class="col-md-3"><input type="text" name="passengers_mobile[]" placeholder="Mobile"  value="" class="form-control"></div></div>';
    }
    document.getElementById('passengers_name').innerHTML = text;
}


function roundA(amt) {
    return amt.toFixed(2);
}

function calculateSalary() {
    var total_advance = 0
    $('input[name="advance_id[]"]').each(function () {

        if ($(this).is(':checked')) {
            var advance_id = $(this).val();
            var advance = Number(_("adv" + advance_id).value);
            total_advance = Number(total_advance) + Number(advance);
        }
    });

    _("adv_amt").value = total_advance;

    var total_ot = 0
    $('input[name="overtime_id[]"]').each(function () {
        if ($(this).is(':checked')) {
            var ot_id = $(this).val();
            var ot = Number(_("ot" + ot_id).value);
            total_ot = Number(total_ot) + Number(ot);
        }
    });

    _("ot_amt").value = total_ot;

    var total_absent = 0
    $('input[name="absent_id[]"]').each(function () {
        if ($(this).is(':checked')) {
            var abs_id = $(this).val();
            var absent = Number(_("abs" + abs_id).value);
            total_absent = Number(total_absent) + Number(absent);
        }
    });

    _("abs_amt").value = total_absent;

    salary = Number(_("salary").value);
    _("paid_amt").value = salary + total_ot - total_absent - total_advance;
}


function tripRating(link, rating) {
    try {
        var data = '';
        $.ajax({
            type: 'GET',
            url: '/trip/rating/' + link + '/' + rating,
            data: data,
            success: function (data) {
                _("rating_text").innerHTML = '<span style="color:green;">Trip Experience was <b>' + data + '</b><br>Thank you for your Ratings.</span>';
            }
        });
    } catch (o) {
        alert(o.message);
    }

}