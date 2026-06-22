import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

const blankMember = { name: '', role: '', photo: '', alt: '' };

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const members = a.members || [];

		const setMember = ( i, key ) => ( v ) => {
			const next = members.map( ( m, idx ) => ( idx === i ? { ...m, [ key ]: v } : m ) );
			setAttributes( { members: next } );
		};
		const addMember = () => setAttributes( { members: [ ...members, { ...blankMember } ] } );
		const removeMember = ( i ) => setAttributes( { members: members.filter( ( _, idx ) => idx !== i ) } );

		const blockProps = useBlockProps( { className: 'drt-team drt-bg-white drt-section' } );

		return (
			<>
				<InspectorControls>
					{ members.map( ( m, i ) => (
						<PanelBody key={ i } title={ `Member ${ i + 1 }${ m.name ? ': ' + m.name : '' }` } initialOpen={ false }>
							<TextControl label="Name" value={ m.name } onChange={ setMember( i, 'name' ) } />
							<TextControl label="Role / title" value={ m.role } onChange={ setMember( i, 'role' ) } />
							<TextControl label="Photo alt (defaults to “Name - Role”)" value={ m.alt } onChange={ setMember( i, 'alt' ) } />
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) => {
										setMember( i, 'photo' )( media.url );
										if ( media.alt ) {
											setMember( i, 'alt' )( media.alt );
										}
									} }
									allowedTypes={ [ 'image' ] }
									render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ m.photo ? 'Replace photo' : 'Pick photo' }</Button> }
								/>
							</MediaUploadCheck>
							<TextControl label="Photo URL" value={ m.photo } onChange={ setMember( i, 'photo' ) } />
							<Button isDestructive variant="link" onClick={ () => removeMember( i ) }>Remove member</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Team members" initialOpen={ members.length === 0 }>
						<Button variant="primary" onClick={ addMember }>Add member</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-section-header">
							<RichText tagName="span" className="drt-eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
							<RichText tagName="h2" className="drt-heading drt-heading--lg drt-text-balance" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body" value={ a.intro } onChange={ set( 'intro' ) } placeholder="Intro…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						</div>
						<div className="drt-team__carousel">
							<div className="swiper">
								<div className="swiper-wrapper">
									{ members.map( ( m, i ) => (
										<div className="swiper-slide" key={ i }>
											<div className="drt-team__member">
												{ m.photo ? (
													<img src={ m.photo } alt={ m.alt || `${ m.name } - ${ m.role }` } className="drt-team__photo" />
												) : (
													<div className="drt-team__photo drt-team__photo--placeholder"><span>{ m.name || 'Photo' }</span></div>
												) }
												<div className="drt-team__overlay">
													<h3 className="drt-team__name">{ m.name }</h3>
													<p className="drt-team__title">{ m.role }</p>
												</div>
											</div>
										</div>
									) ) }
								</div>
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
