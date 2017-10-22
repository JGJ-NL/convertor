currFrom='';
currTo='';
currAmount='';

$(document).ready(function(){
    $('#from, #to').on('change', function(){
        checkChanges();
    });
    $('#amount').on('change keyup paste', function(){
        checkChanges(amount);
    });
});

function checkChanges()
{
    var from = $('#from').val();
    var to = $('#to').val();
    var amount = $('#amount').val()
    amount = parseFloat(amount.trim(amount.replace(' ', '')));
    amount = isNaN(amount) ? '' : amount;
    if (from!='' && to!='' && amount!='' &&
         (currFrom!=from || currTo!=to || currAmount!=amount)
    ) {
        // Save current latest values (as they may change before rate is known)
        currFrom = from;
        currTo = to;
        currAmount = amount;
        // Ensure to not show invalid conversion, while waiting for new rate
        clearConversion();
        // Get rate
        $.ajax({url: 'getrate',
            type: "GET",
            data: "from="+from+"&to="+to,
            cache: false,
            success: function(response){
                var obj = JSON.parse(response);
                if (obj.from==currFrom && obj.to==currTo) {
                    if(obj.error) {
                        // Display error message
                        alert(('ERROR: ' + obj.error));
                    } else {
                        // Convert amount and update screen
                        updateRateAndConversion(obj.from, obj.to, obj.rate, obj.back, obj.date)
                    }
                }
            },
            error: function(){
                clearConversion();
            }
        });
    }
}

function updateRateAndConversion(from, to, rate, back, date, amount)
{
    if (rate<=0) {
        // No rate available, so clear any possible 'old' conversion
        clearConversion();
    } else {
       // Update conversion with latest amount set, as this may have changed in the mean time
       var amount = $('#amount').val();
       var result = '' + (parseFloat((''+(rate * amount))).toFixed(2));
       $('#converted').html(result);
       
       // Update rate lines
       var preDate = 'as on ';
       var betweenCurr = ' / ';
       var postCurr = '&nbsp; :';
       $('#rateforward .ratecurrencies').html(from + ' / ' + to + '&nbsp; :');
       $('#rateforward .ratevalue').html('' + rate);
       $('#rateforward .ratedate').html(preDate + date);
       
       if (back<=0) {
           // No back rate available, so ensure an empty back rate line
           from = '';
           to = '';
           back = '';
           date = '';
           preDate = '';
           betweenCurr = '';
           postCurr = '';
       }
       $('#ratebackward .ratecurrencies').html(to + betweenCurr + from + postCurr);
       $('#ratebackward .ratevalue').html('' + back);
       $('#ratebackward .ratedate').html(preDate + date);
       
       checkGraphUpdate();
    }
}

function clearConversion()
{
    $('#converted').html('');
}

function checkGraphUpdate() 
{
    if (!graphCurrencies()) {
        clearGraph();
    } else {
        var canvas = document.getElementById("graphcanvas");
        $.ajax({url: 'getgraph',
            type: "GET",
            data: "from="+currFrom+"&to="+currTo+"&w="+canvas.width+"&h="+canvas.height,
            cache: false,
            success: function(response){
                var obj = JSON.parse(response);
                if ((obj.from==currFrom && obj.to==currTo) ||
                    (obj.from==currTo && obj.to==currFrom)
                ) {
                    // Convert amount and update screen
                    drawGraph(obj);
                }
            },
            error: function(){
                clearConversion();
            }
        });
    }
}

function graphCurrencies()
{
    if ((currFrom=='EUR' && (currTo=='GBP' || currTo=='USD')) ||
        (currTo=='EUR' &&(currFrom=='GBP' || currFrom=='USD')) 
    ){
        return true;
    } else {
        return false;
    }
}

function clearGraph()
{
    var canvas = document.getElementById("graphcanvas");
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.canvas.width = ctx.canvas.width;
}

function drawGraph(obj)
{
    if (obj.rates[0]) {
        clearGraph();
        
        var canvas = document.getElementById("graphcanvas");
        var ctx = canvas.getContext("2d");
        var w = canvas.width;
        var h = canvas.height;
        
        pixelsPerRate = calcRateWidth(w,obj.rates.length);
        gridCellHeight = calcGridCellHeight(h, obj.grid.length);
        startX = getGridStartX(w, obj, pixelsPerRate);
        startY = getGridStartY(h, obj, gridCellHeight);
        
        drawGraphHeader(ctx, obj, w);
        drawGrid(ctx, obj, startX, startY, pixelsPerRate, gridCellHeight);
        drawRates(ctx, obj, startX, startY, pixelsPerRate, gridCellHeight);
    }
}

function getGridStartX(canvasWidth, obj, rateW)
{
    var fullWidth = obj.rates.length * rateWidth;
    var startX = Math.max(2, Math.floor((((canvasWidth * 0.9) - fullWidth)/2)));
    return startX;
}

function getGridStartY(canvasHeight, obj, cellHeight)
{
    var fullHeight = (obj.grid.length-1) * cellHeight;
    var startY = Math.floor(((canvasHeight-fullHeight)/2));
    return startY
}

function calcRateWidth(canvasWidth, numRates)
{
    // Allow approx 90% of the canvas width for the graph
    rateWidth = (canvasWidth * 0.9) / numRates;
    if(rateWidth >= 1.0000) {
        rateWidth = Math.floor(rateWidth);
    } else if (rateWidth >= 0.98) {
        rateWidth = 1;
    }
    return rateWidth;
}

function calcGridCellHeight(canvasHeight, numGridLines)
{
    // Allow approx 80% of the canvas height for the grid
    cellHeight = (canvasHeight) * 0.8 / (numGridLines - 1);
    cellHeight = Math.floor(cellHeight);
    return cellHeight;
}

function drawGraphHeader(ctx, obj, canvasWidth)
{
    var currentAlign = ctx.textAlign;
    var currentStyle = ctx.fillStyle;
    
    ctx.fillStyle = '#555577';
    ctx.fillText((obj.from + ' / ' + obj.to), 2, 10);
    
    ctx.textAlign = "right";
    ctx.fillText((obj.days[4] + ', ' + obj.time), (canvasWidth-2), 10);
    
    ctx.textAlign = currentAlign;
    ctx.fillStyle = currentStyle;
}

function drawGrid(ctx, obj, startX, startY, rateWidth, cellHeight)
{
    var currentStyle = ctx.strokeStyle;
    
    var x;
    var y;
    
    var fullWidth = obj.rates.length * rateWidth;
    var fullHeight = (obj.grid.length-1) * cellHeight;
    
    ctx.strokeStyle = '#eeeeee';
    
    var horLine = 0;
    while (horLine<obj.grid.length) {
     y = startY + (horLine * cellHeight);
     x = startX + fullWidth;
     ctx.moveTo(startX, y);
     ctx.lineTo(x, y);
     ctx.stroke();
     // Put the gird value behind it, that are sorted the other way around(!)
     ctx.fillText((''+obj.grid[obj.grid.length-horLine-1]), x+10, y);
     horLine++;
    }
    
    var vertLine = 0;
    var numVertLines = obj.days.length + 1;
    var cellWidth = 24 * rateWidth;
    while (vertLine<numVertLines) {
     x = startX + (vertLine * cellWidth);
     y = startY + fullHeight;
     ctx.moveTo(x, startY);
     ctx.lineTo(x,y);
     ctx.stroke();
     if (vertLine<(numVertLines-1)) {
         ctx.fillText(obj.days[vertLine], x+10, y+20);
     }
     vertLine++;
    }
    
    ctx.strokeStyle = currentStyle;
}

function drawRates(ctx, obj, startX, startY, rateWidth, cellHeight)
{
    var currentStyle = ctx.strokeStyle;
    ctx.strokeStyle = '#334488';
    
    var numRates = obj.rates.length;
    // Add one extra rate to the end to ensure that we always have a zero value at the end.
    // This will make testing for the end of a line piece easier in the while loop below.
    obj.rates[obj.rates.length] = 0;
    
    var x;
    var y;
    var fullHeight = cellHeight * (obj.grid.length - 1);
    
    var minRate = obj.grid[0];
    var maxRate = obj.grid[obj.grid.length-1];
    var shownRates = maxRate - minRate;
    
    var rateIndex = 0;
    while (obj.rates[rateIndex]>0) {
        y = startY + fullHeight - (((obj.rates[rateIndex] - minRate)/shownRates)*fullHeight);
        if (rateIndex==0) {
            // We have to set a start point
            x = startX;
            y = startY + fullHeight - (((obj.rates[rateIndex] - minRate)/shownRates)*fullHeight);
            ctx.moveTo(x,y);
        } else {
            // Draw vertical line from the previous rateIndex X position
            x = startX + (rateIndex * rateWidth);
            ctx.lineTo(x,y);
        }
        rateIndex++;
        
        // To not just draw every line piece with the same rate value separately,
        // we check if next rate(s) contain the same rate value and draw them all in one go.
        indexIncrement = 0;
        while (obj.rates[rateIndex+indexIncrement] == obj.rates[rateIndex-1]) {
         indexIncrement++;
        }
        if (indexIncrement>0) {
            rateIndex += indexIncrement;
            x = startX + (rateIndex * rateWidth);
            ctx.lineTo(x, y);
        }
    }
    ctx.stroke();
    
    ctx.strokeStyle = currentStyle;
}

