var arr = []; 
var item = {
  id: '21',
  step: 'step2',
  label: 'Banana',
  price: '19$'
};

arr.push(item);
/* note : data is the actual object that matched search criteria 
  or undefined if nothing matched */
var data = arr.find( function( ele ) { 
    return ele.id === '21';
} );

if( data ) {
 console.log( 'found' );
 console.log(data); // This is entire object i.e. `item` not boolean
}