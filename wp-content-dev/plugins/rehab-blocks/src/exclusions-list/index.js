import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextareaControl } from '@wordpress/components';
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
					<PanelBody title="Exclusions" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] }
							onChange={ set( 'background' ) }
						/>
						<TextareaControl
							label="Excluded items (one per line)"
							value={ ( a.items || [] ).join( '\n' ) }
							onChange={ ( v ) => setAttributes( { items: v.split( '\n' ).filter( ( s ) => s.trim() !== '' ) } ) }
							rows={ 9 }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-exclusions rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-exclusions__head">
								<RichText tagName="span" className="rehab-exclusions__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-exclusions__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
								<RichText tagName="p" className="rehab-exclusions__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Lede…" allowedFormats={ [] } />
							</div>
							<div className="rehab-exclusions__grid">
								{ ( a.items || [] ).map( ( item, i ) => (
									<div className="rehab-exclusions__item" key={ i }><span className="rehab-exclusions__x" /> { item }</div>
								) ) }
							</div>
							<RichText tagName="p" className="rehab-exclusions__note" value={ a.note } onChange={ set( 'note' ) } placeholder="Reassurance note…" allowedFormats={ [] } />
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
