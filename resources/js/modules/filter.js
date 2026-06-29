$(function () {
  
        // --- Daterangepicker setup (only if #daterange exists) ---
    if ($("#daterange").length > 0) {
        import("daterangepicker").then(() => {
            import("daterangepicker/daterangepicker.css");

            $("#daterange").daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: "Clear",
                    format: "YYYY-MM-DD",
                    separator: " to ",
                },
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [
                        moment().subtract(1, "days"),
                        moment().subtract(1, "days"),
                    ],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [
                        moment().startOf("month"),
                        moment().endOf("month"),
                    ],
                    "Last Month": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
                    ],
                },
            });

            $("#daterange").on("apply.daterangepicker", function (ev, picker) {
                $(this).val(
                    picker.startDate.format("YYYY-MM-DD") +
                    " to " +
                    picker.endDate.format("YYYY-MM-DD"),
                );
            });

            $("#daterange").on("cancel.daterangepicker", function (ev, picker) {
                $(this).val("");
            });
        });
    }
    
    // --- Generic DataTable filter (auto-detects the table) ---
    if ($("#filter-form").length === 0) return;

    // Find the first DataTable instance on the page
    let dataTable = null;
    let tableKeys = Object.keys(window.LaravelDataTables || {});
    if (tableKeys.length > 0) {
        dataTable = window.LaravelDataTables[tableKeys[0]];
    }

    if (!dataTable) return;

    // Collect all filter values from #filter-form by their name attribute
    function getFilters() {
        let filters = {};
        $("#filter-form")
            .find("select, input")
            .each(function () {
                let name = $(this).attr("name");
                let value = $(this).val();
                if (name && value) {
                    let normalizedName = name.replace(/\[\]$/, '');
                    filters[normalizedName] = value;
                }
            });
        return filters;
    }

    // Attach filter data to every DataTable AJAX request
    dataTable.on("preXhr.dt", function (e, settings, data) {
        data.filters = getFilters();
    });

    // Apply filters
    $("#apply-filter").on("click", function () {
        dataTable.ajax.reload();
    });

    // Reset filters
    $("#reset-filter").on("click", function () {
        $("#filter-form")[0].reset();
        $("#daterange").val("");
        // Reset Select2 dropdowns
        $(".leaveSelectSearch").val(null).trigger("change");
        dataTable.ajax.reload();
    });
});
