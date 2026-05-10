import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function Edit( { attributes, setAttributes } ) {
	const { background, speed, items } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-marquee rehab-bg-${ background }`,
		style: { '--rehab-marquee-duration': `${ speed }s` },
	} );

	const updateItem = ( idx, value ) =>
		setAttributes( { items: items.map( ( it, i ) => ( i === idx ? value : it ) ) } );

	const addItem = () => setAttributes( { items: [ ...items, 'New item' ] } );

	const removeItem = ( idx ) => setAttributes( { items: items.filter( ( _, i ) => i !== idx ) } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Marquee settings', 'rehab-blocks' ) } initialOpen>
					<RangeControl
						label={ __( 'Speed (seconds per loop)', 'rehab-blocks' ) }
						value={ speed }
						min={ 10 }
						max={ 120 }
						step={ 5 }
						onChange={ ( v ) => setAttributes( { speed: v } ) }
					/>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'Sage mist', value: 'sage-mist' },
							{ label: 'Cream', value: 'cream' },
							{ label: 'White', value: 'white' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
					<Button variant="secondary" onClick={ addItem } style={ { marginTop: '1rem' } }>
						{ __( '+ Add item', 'rehab-blocks' ) }
					</Button>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-marquee__editor-list">
					{ items.map( ( it, idx ) => (
						<div key={ idx } className="rehab-marquee__editor-item">
							<RichText
								tagName="span"
								value={ it }
								onChange={ ( v ) => updateItem( idx, v ) }
								placeholder={ __( 'Item text', 'rehab-blocks' ) }
								allowedFormats={ [] }
							/>
							<button
								type="button"
								className="rehab-marquee__remove"
								onClick={ () => removeItem( idx ) }
								aria-label={ __( 'Remove', 'rehab-blocks' ) }
							>
								×
							</button>
						</div>
					) ) }
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, speed, items } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-marquee rehab-bg-${ background }`,
		style: { '--rehab-marquee-duration': `${ speed }s` },
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-marquee__track">
				{ /* Duplicate items so the loop is seamless */ }
				{ [ 0, 1 ].map( ( clone ) => (
					<div className="rehab-marquee__row" aria-hidden={ clone === 1 ? 'true' : undefined } key={ clone }>
						{ items.map( ( it, idx ) => (
							<span className="rehab-marquee__item" key={ `${ clone }-${ idx }` }>
								<span className="rehab-marquee__diamond" aria-hidden="true">◆</span>
								<RichText.Content tagName="span" value={ it } />
							</span>
						) ) }
					</div>
				) ) }
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
