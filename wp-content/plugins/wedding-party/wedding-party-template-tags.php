<?php

Class Wedding_Party {

  var $post_id;
  var $impt_roles;

  public function __construct( $post ) {
    $this->post_id = $post->ID;
    $this->impt_roles = array( 'bride', 'groom', 'maid-of-honor', 'best-man', );
  }

  private function get_field( $field ) {
    return get_post_meta( $this->post_id, $field, true);
  }

  public function get_role() {
    return $this->get_field( 'role' );
  }

  public function get_relationship() {
    return $this->get_field( 'relationship' );
  }

  public function get_head_shot() {
    return get_the_post_thumbnail( $this->post_id, 'thumbnail' );
  }

  public function is_important_role() {
    $role = $this->get_role();
    //standardize format
    $role = strtolower( trim( str_replace( ' ', '-', $role ) ) );
    return in_array( $role, $this->impt_roles );
  }

}
