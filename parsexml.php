<?
/**
 * m_i_h_k_e_l_AT_w_w_DOT_e_e
 * 26.10.2003
**/
$filename = "m.m.xml";
$xmlC = new XmlC();
$xml_data = file_get_contents( $filename );

$xmlC->Set_XML_data( $xml_data );

echo( "<pre>\n" );
print_r( $xmlC->obj_data );
echo( "</pre>\n" );

class XmlC
{
  var $xml_data;
  var $obj_data;
  var $pointer;

  function XmlC()
  {
  }

  function Set_xml_data( &$xml_data )
  {
   $this->index = 0;
   $this->pointer[] = &$this->obj_data;

   $this->xml_data = $xml_data;
   $this->xml_parser = xml_parser_create( "UTF-8" );

   xml_parser_set_option( $this->xml_parser, XML_OPTION_CASE_FOLDING, false );
   xml_set_object( $this->xml_parser, &$this );
   xml_set_element_handler( $this->xml_parser, "_startElement", "_endElement");
   xml_set_character_data_handler( $this->xml_parser, "_cData" );

   xml_parse( $this->xml_parser, $this->xml_data, true );
   xml_parser_free( $this->xml_parser );
  }

  function _startElement( $parser, $tag, $attributeList )
  {
   foreach( $attributeList as $name => $value )
   {
     $value = $this->_cleanString( $value );
     $object->$name = $value;
   }
   eval( "\$this->pointer[\$this->index]->" . $tag . "[] = \$object;" );
   eval( "\$size = sizeof( \$this->pointer[\$this->index]->" . $tag . " );" );
   eval( "\$this->pointer[] = &\$this->pointer[\$this->index]->" . $tag . "[\$size-1];" );
  
   $this->index++;
  }

  function _endElement( $parser, $tag )
  {
   array_pop( $this->pointer );
   $this->index--;
  }

  function _cData( $parser, $data )
  {
   if( trim( $data ) )
   {
     $this->pointer[$this->index] = trim( $data );
   }
  }

  function _cleanString( $string )
  {
   return utf8_decode( trim( $string ) );
  }

}
?>