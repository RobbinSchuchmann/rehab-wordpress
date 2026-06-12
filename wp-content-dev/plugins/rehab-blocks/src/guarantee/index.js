import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Section" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] }
							onChange={ set( 'background' ) }
						/>
						<TextControl label="Outline button text" value={ a.ghostText } onChange={ set( 'ghostText' ) } />
						<TextControl label="Outline button URL" value={ a.ghostUrl } onChange={ set( 'ghostUrl' ) } />
					</PanelBody>
					<PanelBody title="Offer card" initialOpen={ false }>
						<TextareaControl
							label="Terms (one per line)"
							value={ ( a.terms || [] ).join( '\n' ) }
							onChange={ ( v ) => setAttributes( { terms: v.split( '\n' ).filter( ( s ) => s.trim() !== '' ) } ) }
							rows={ 5 }
						/>
						<TextControl label="Card button text" value={ a.cardBtnText } onChange={ set( 'cardBtnText' ) } />
						<TextControl label="Card button URL" value={ a.cardBtnUrl } onChange={ set( 'cardBtnUrl' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-guarantee rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-guarantee__grid">
								<div className="rehab-guarantee__copy">
									<RichText tagName="span" className="rehab-guarantee__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-guarantee__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
									<RichText tagName="p" value={ a.body } onChange={ set( 'body' ) } placeholder="Body (blank line between paragraphs)…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									{ a.ghostText ? <span className="rehab-btn rehab-btn--outline">{ a.ghostText }</span> : null }
								</div>
								<aside className="rehab-guarantee__card">
									<RichText tagName="span" className="rehab-guarantee__card-eyebrow" value={ a.cardEyebrow } onChange={ set( 'cardEyebrow' ) } placeholder="Card eyebrow…" allowedFormats={ [] } />
									<RichText tagName="div" className="rehab-guarantee__card-big" value={ a.cardBig } onChange={ set( 'cardBig' ) } placeholder="Big line…" allowedFormats={ [] } />
									<RichText tagName="p" className="rehab-guarantee__card-sub" value={ a.cardSub } onChange={ set( 'cardSub' ) } placeholder="Sub line…" allowedFormats={ [] } />
									<ul className="rehab-guarantee__terms">
										{ ( a.terms || [] ).map( ( t, i ) => <li key={ i }>✓ { t }</li> ) }
									</ul>
									{ a.cardBtnText ? <span className="rehab-btn rehab-btn--luxury rehab-btn--block">{ a.cardBtnText }</span> : null }
								</aside>
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
