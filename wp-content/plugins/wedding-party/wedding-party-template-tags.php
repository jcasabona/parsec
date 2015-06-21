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

  private function get_sanitized_role() {
    $role = $this->get_role();
    return sanitize_title_with_dashes( $role );
  }

  public function get_role() {
    return $this->get_field( 'role' );
  }

  public function get_relationship() {
    return $this->get_field( 'relationship' );
  }

  public function get_party( $uc = true ) {
    if ( $uc ) {
      return ucfirst( $this->get_field( 'party_side' ) );
    }

    return $this->get_field( 'party_side' );
  }

  public function get_head_shot( $size = 'thumbnail' ) {
    return get_the_post_thumbnail( $this->post_id, $size );
  }

  public function is_important_role() {
    return in_array( $this->get_sanitized_role(), $this->impt_roles );
  }

  public function get_classes( $classes = '' ) {
    $classes .= ( $this->is_important_role() ) ? ' important-role ' : '';
    $classes .= $this->get_party( false ) . '-party ';
    $classes .= $this->get_sanitized_role();

    return trim( $classes );
  }

/* @TODO: Get Wedding Party Members: general private function
 * get_party_members( $side = 'bride' ) then
 * get_bridesmaids() & get_groomsmen()
*/
}
